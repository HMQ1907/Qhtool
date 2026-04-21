<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class VideoCleanupService
{
    public function cleanupSubtitle(string $inputRelativePath, string $outputRelativePath, array $region = []): string
    {
        $inputPath = Storage::disk('public')->path($inputRelativePath);
        $outputPath = Storage::disk('public')->path($outputRelativePath);

        $this->assertBinaryExists(config('video_cleanup.ffmpeg_bin'), 'FFmpeg');
        $this->assertBinaryExists(config('video_cleanup.ffprobe_bin'), 'FFprobe');

        $videoInfo = $this->probeVideo($inputPath);
        $resolvedRegion = $this->resolveRegion(
            $videoInfo['width'],
            $videoInfo['height'],
            $region
        );

        Storage::disk('public')->makeDirectory(dirname($outputRelativePath));

        $filter = sprintf(
            'delogo=x=%d:y=%d:w=%d:h=%d:band=%d:show=0',
            $resolvedRegion['x'],
            $resolvedRegion['y'],
            $resolvedRegion['w'],
            $resolvedRegion['h'],
            $resolvedRegion['band']
        );

        $process = new Process([
            config('video_cleanup.ffmpeg_bin'),
            '-y',
            '-i',
            $inputPath,
            '-vf',
            $filter,
            '-map',
            '0:v:0',
            '-map',
            '0:a?',
            '-c:v',
            'libx264',
            '-preset',
            'medium',
            '-crf',
            '18',
            '-pix_fmt',
            'yuv420p',
            '-c:a',
            'aac',
            '-b:a',
            '192k',
            '-movflags',
            '+faststart',
            $outputPath,
        ]);

        $process->setTimeout((int) config('video_cleanup.timeout', 900));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'Khong the xu ly video.');
        }

        if (! is_file($outputPath)) {
            throw new RuntimeException('Khong tim thay file video da xu ly.');
        }

        return $outputRelativePath;
    }

    private function probeVideo(string $inputPath): array
    {
        $process = new Process([
            config('video_cleanup.ffprobe_bin'),
            '-v',
            'error',
            '-select_streams',
            'v:0',
            '-show_entries',
            'stream=width,height',
            '-of',
            'json',
            $inputPath,
        ]);

        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'Khong doc duoc metadata video.');
        }

        $payload = json_decode($process->getOutput(), true);
        $stream = $payload['streams'][0] ?? null;
        $width = (int) ($stream['width'] ?? 0);
        $height = (int) ($stream['height'] ?? 0);

        if ($width <= 0 || $height <= 0) {
            throw new RuntimeException('Khong xac dinh duoc kich thuoc video.');
        }

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    private function resolveRegion(int $videoWidth, int $videoHeight, array $region): array
    {
        $defaults = config('video_cleanup.default_region');

        $leftPct = (float) ($region['left_pct'] ?? $defaults['left_pct']);
        $topPct = (float) ($region['top_pct'] ?? $defaults['top_pct']);
        $widthPct = (float) ($region['width_pct'] ?? $defaults['width_pct']);
        $heightPct = (float) ($region['height_pct'] ?? $defaults['height_pct']);
        $band = max(1, (int) ($defaults['band'] ?? 14));

        $x = max(0, (int) round($videoWidth * $leftPct / 100));
        $y = max(0, (int) round($videoHeight * $topPct / 100));
        $w = max(2, (int) round($videoWidth * $widthPct / 100));
        $h = max(2, (int) round($videoHeight * $heightPct / 100));

        if ($x + $w > $videoWidth) {
            $w = max(2, $videoWidth - $x);
        }

        if ($y + $h > $videoHeight) {
            $h = max(2, $videoHeight - $y);
        }

        if ($w < 2 || $h < 2) {
            throw new RuntimeException('Vung xoa subtitle khong hop le.');
        }

        return compact('x', 'y', 'w', 'h', 'band');
    }

    private function assertBinaryExists(?string $binary, string $label): void
    {
        if (blank($binary)) {
            throw new RuntimeException("Chua cau hinh {$label} binary.");
        }

        $process = new Process([$binary, '-version']);
        $process->setTimeout(30);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException("Khong tim thay {$label}. Hay cai dat va set bien moi truong.");
        }
    }
}
