<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use Symfony\Component\Process\Process;

class RenderFinalVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

    public function __construct(public CampaignVideo $video) {}

    public function handle(): void
    {
        $this->video->update(['status' => 'rendering']);

        try {
            if (empty($this->video->voice_audio_url)) {
                throw new RuntimeException('Missing generated voice audio.');
            }

            $finalVideoUrl = $this->renderWithVoiceAndCaptions();

            $this->video->update([
                'final_video_url' => $finalVideoUrl,
                'status' => 'completed',
            ]);

            $campaign = $this->video->campaign;
            $remaining = $campaign->videos()
                ->whereNotIn('status', ['completed', 'failed'])
                ->count();

            if ($remaining === 0) {
                $campaign->update([
                    'status' => $campaign->videos()->where('status', 'failed')->exists()
                        ? 'failed'
                        : 'completed',
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Render Final Video Error: ' . $e->getMessage());

            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->video->campaign->update(['status' => 'failed']);
            throw $e;
        }
    }

    private function renderWithVoiceAndCaptions(): string
    {
        $workDir = storage_path('app/private/render/' . $this->video->id . '-' . uniqid());
        if (! is_dir($workDir)) {
            mkdir($workDir, 0775, true);
        }

        $backgroundPath = $workDir . DIRECTORY_SEPARATOR . 'background.mp4';
        $audioPath = $this->localPublicPathFromUrl($this->video->voice_audio_url);
        $captionPath = $workDir . DIRECTORY_SEPARATOR . 'captions.ass';
        $outputRelativePath = 'final-videos/' . $this->video->id . '.mp4';
        $outputPath = Storage::disk('public')->path($outputRelativePath);

        if (! empty($this->video->background_video_url)) {
            $this->downloadToPath($this->video->background_video_url, $backgroundPath);
        } else {
            $this->renderProductBackground($workDir, $backgroundPath, $audioPath);
        }

        $this->writeCaptionFile($captionPath, $audioPath);

        if (! is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0775, true);
        }

        $captioned = $this->runFfmpeg($backgroundPath, $audioPath, $captionPath, $outputPath, true);
        if (! $captioned) {
            Log::warning('Caption burn failed, rendering final video with audio only.', [
                'video_id' => $this->video->id,
            ]);

            $this->runFfmpeg($backgroundPath, $audioPath, null, $outputPath, false);
        }

        return Storage::url($outputRelativePath);
    }

    private function downloadToPath(string $url, string $path): void
    {
        $response = Http::timeout(180)->get($url);
        if ($response->failed()) {
            throw new RuntimeException('Failed to download background video: ' . $url);
        }

        file_put_contents($path, $response->body());
    }

    private function renderProductBackground(string $workDir, string $backgroundPath, string $audioPath): void
    {
        $images = $this->visualImagesForRender($workDir);
        if (empty($images)) {
            throw new RuntimeException('Missing product images for local affiliate render.');
        }

        $audioDuration = $this->mediaDuration($audioPath) ?: (float) $this->video->duration_seconds;
        $targetDuration = max(12.0, $audioDuration);
        $imagePaths = array_map(fn (string $image) => Storage::disk('public')->path($image), $images);
        $imagePaths = array_values(array_filter($imagePaths, fn (string $path) => file_exists($path)));

        if (empty($imagePaths)) {
            throw new RuntimeException('Product image files do not exist locally.');
        }

        $segmentDuration = max(2.5, $targetDuration / count($imagePaths));
        $segments = [];

        foreach ($imagePaths as $index => $imagePath) {
            $segmentPath = $workDir . DIRECTORY_SEPARATOR . "segment_{$index}.mp4";
            $frames = (int) ceil($segmentDuration * 30);
            $zoomDirection = $index % 2 === 0
                ? "min(zoom+0.0012,1.08)"
                : "max(zoom-0.0010,1.0)";
            $initialZoom = $index % 2 === 0 ? '1.0' : '1.08';

            $filter = "scale=1200:2134:force_original_aspect_ratio=increase,crop=1200:2134,"
                . "zoompan=z='if(eq(on,0),{$initialZoom},{$zoomDirection})':d={$frames}:s=1080x1920:fps=30,"
                . "eq=contrast=1.05:brightness=-0.015:saturation=1.08,format=yuv420p";

            $process = new Process([
                config('evolink.ffmpeg_path', 'ffmpeg'),
                '-y',
                '-loop',
                '1',
                '-i',
                $imagePath,
                '-vf',
                $filter,
                '-frames:v',
                (string) $frames,
                '-c:v',
                'libx264',
                '-preset',
                'veryfast',
                '-crf',
                '21',
                '-an',
                $segmentPath,
            ]);

            $process->setTimeout(300);
            $process->run();

            if (! $process->isSuccessful()) {
                throw new RuntimeException('FFmpeg product segment render failed: ' . $process->getErrorOutput());
            }

            $segments[] = $segmentPath;
        }

        $concatPath = $workDir . DIRECTORY_SEPARATOR . 'segments.txt';
        file_put_contents(
            $concatPath,
            implode('', array_map(fn (string $path) => "file '" . str_replace("'", "'\\''", $path) . "'" . PHP_EOL, $segments))
        );

        $process = new Process([
            config('evolink.ffmpeg_path', 'ffmpeg'),
            '-y',
            '-f',
            'concat',
            '-safe',
            '0',
            '-i',
            $concatPath,
            '-c',
            'copy',
            $backgroundPath,
        ]);

        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('FFmpeg product background concat failed: ' . $process->getErrorOutput());
        }
    }

    private function visualImagesForRender(string $workDir): array
    {
        $generated = $this->video->generated_product_images ?: [];
        $generated = array_values(array_filter($generated, fn (string $image) => file_exists(Storage::disk('public')->path($image))));

        if (! empty($generated)) {
            return $generated;
        }

        if ($this->video->generation_mode === 'fast_test' || ! config('evolink.generate_product_images', true)) {
            return $this->video->product_images ?: [];
        }

        try {
            $generated = $this->generateProductScenes($workDir);

            if (! empty($generated)) {
                $this->video->update(['generated_product_images' => $generated]);
                return $generated;
            }
        } catch (Throwable $e) {
            Log::warning('AI product scene generation failed, falling back to uploaded images.', [
                'video_id' => $this->video->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->video->product_images ?: [];
    }

    private function generateProductScenes(string $workDir): array
    {
        $sourceImages = array_slice($this->video->product_images ?: [], 0, 5);
        if (count($sourceImages) < 1) {
            return [];
        }

        $referenceUrls = [];
        foreach ($sourceImages as $image) {
            $path = Storage::disk('public')->path($image);
            if (file_exists($path)) {
                $referenceUrls[] = $this->uploadReferenceImage($path);
            }
        }

        if (empty($referenceUrls)) {
            return [];
        }

        $count = max(1, min(3, (int) config('evolink.product_scene_count', 2)));
        $generated = [];

        for ($i = 0; $i < $count; $i++) {
            $task = $this->requestProductScene($referenceUrls, $i);
            $imageUrl = $this->extractResultUrl($task, ['url', 'image_url', 'file_url']);
            $generated[] = $this->downloadGeneratedImage($imageUrl, $i);
        }

        return $generated;
    }

    private function uploadReferenceImage(string $path): string
    {
        $response = Http::timeout(180)
            ->withToken(config('evolink.api_key'))
            ->attach('file', file_get_contents($path), basename($path))
            ->post(rtrim(config('evolink.file_upload_base_url'), '/') . '/api/v1/files/upload/stream');

        if ($response->failed() || ! $response->json('success')) {
            throw new RuntimeException('Failed to upload product reference image: ' . $response->body());
        }

        $url = $response->json('data.file_url') ?: $response->json('data.download_url');
        if (! $url) {
            throw new RuntimeException('File upload did not return a usable file URL: ' . $response->body());
        }

        return $url;
    }

    private function requestProductScene(array $referenceUrls, int $index): array
    {
        $response = Http::timeout(120)
            ->withToken(config('evolink.api_key'))
            ->post($this->endpoint('/images/generations'), [
                'model' => $this->imageModel(),
                'prompt' => $this->buildProductScenePrompt($index),
                'image_urls' => $referenceUrls,
                'size' => '9:16',
                'quality' => config('evolink.image_quality', '1K'),
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Evolink image generation init failed: ' . $response->body());
        }

        $taskId = $response->json('id');
        if (! $taskId) {
            throw new RuntimeException('Evolink image generation did not return a task ID: ' . $response->body());
        }

        return $this->waitForTask($taskId, 'image generation task', (int) config('evolink.video_timeout', 900));
    }

    private function buildProductScenePrompt(int $index): string
    {
        $scenes = $this->sceneSetForProduct();
        $modeStyle = match ($this->video->generation_mode) {
            'winner_scale' => 'conversion focused product proof, clear close-up details, practical use-case sequence, realistic TikTok Shop review look',
            'premium_product' => 'premium lifestyle product photography, tasteful composition, polished but believable social commerce visual',
            default => 'authentic TikTok product review visual, practical daily-use scene, natural phone-camera realism',
        };

        return implode(', ', [
            'Create a new vertical 9:16 product advertising image using the uploaded product photos as strict reference',
            'Product: ' . $this->video->product_name,
            'Product notes: ' . ($this->video->product_description ?: 'infer only safe visible product context from references'),
            'Buyer pain point: ' . ($this->video->product_pain_points ?: 'daily inconvenience solved by the product'),
            $scenes[$index % count($scenes)],
            'preserve the real product shape, color, material, proportions, labels if visible but do not invent readable text, and distinctive details',
            $modeStyle,
            'show the product as the hero object, large enough to inspect, not tiny, not hidden by props',
            'no people, no hands, no text, no logos, no watermark, no UI, no price tags, no readable letters',
            'leave clean lower-middle space for subtitles',
        ]);
    }

    private function sceneSetForProduct(): array
    {
        $text = mb_strtolower(implode(' ', array_filter([
            $this->video->product_name,
            $this->video->product_description,
            $this->video->product_pain_points,
        ])), 'UTF-8');

        $sets = [
            'kitchen' => [
                'a compact real kitchen counter scene, tidy small apartment kitchen, useful product placement, morning natural light',
                'a before-after inspired kitchen organization scene, clean countertop, realistic home lighting, product solving clutter',
                'a close-up on the product near sink or cooking prep area, practical household review photography',
            ],
            'beauty' => [
                'a clean bathroom vanity or makeup table scene, soft daylight, realistic beauty product review setup',
                'a close-up product detail scene beside mirror and simple skincare items, neat premium composition',
                'a small dressing table lifestyle scene, calm natural light, product easy to inspect',
            ],
            'electronics' => [
                'a modern desk setup scene, phone or laptop nearby, tidy workspace, product shown in practical tech use context',
                'a close-up product detail scene on a clean desk, cable or accessory context if relevant, realistic lighting',
                'a small apartment work corner scene, useful gadget review style, sharp product focus',
            ],
            'fashion' => [
                'a clean wardrobe or dressing corner scene, neutral background, product styled as a daily outfit accessory',
                'a close-up fabric or accessory detail scene, premium but realistic ecommerce review photography',
                'a minimal lifestyle flat-lay scene with the product as the hero, soft natural light',
            ],
            'mom_baby' => [
                'a tidy family home scene, soft safe nursery-like lighting, product shown as practical daily helper',
                'a clean table scene with gentle colors, family-use context, product visible and trustworthy',
                'a close-up of the product in a calm home routine setting, realistic social commerce visual',
            ],
            'home' => [
                'a small apartment living area scene, clean and practical, product solving a visible home inconvenience',
                'a tidy shelf or tabletop scene, realistic household review visual, product as hero object',
                'a close-up home organization scene, warm natural light, useful daily-life context',
            ],
        ];

        $keywords = [
            'kitchen' => ['bep', 'nha bep', 'noi', 'chao', 'dao', 'thot', 'chen', 'bat', 'rua', 'gia vi', 'dau an', 'hop dung', 'ke dung'],
            'beauty' => ['kem', 'son', 'duong', 'serum', 'toc', 'da', 'mat na', 'trang diem', 'makeup', 'my pham'],
            'electronics' => ['sac', 'usb', 'dien', 'den led', 'tai nghe', 'loa', 'ban phim', 'chuot', 'camera', 'may hut bui', 'quat'],
            'fashion' => ['ao', 'quan', 'vay', 'giay', 'tui', 'non', 'kinh', 'dong ho', 'thoi trang'],
            'mom_baby' => ['be', 'em be', 'tre', 'me va be', 'binh sua', 'bim', 'do choi', 'an dam'],
        ];

        foreach ($keywords as $key => $items) {
            foreach ($items as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $sets[$key];
                }
            }
        }

        return $sets['home'];
    }

    private function downloadGeneratedImage(string $url, int $index): string
    {
        $response = Http::timeout(180)->get($url);
        if ($response->failed()) {
            throw new RuntimeException('Failed to download generated product image: ' . $url);
        }

        $relativePath = 'generated-product-scenes/' . $this->video->id . '-' . $index . '-' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($relativePath, $response->body());

        return $relativePath;
    }

    private function localPublicPathFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        $localPath = Storage::disk('public')->path($path);
        if (! file_exists($localPath)) {
            throw new RuntimeException('Voice audio file does not exist locally: ' . $localPath);
        }

        return $localPath;
    }

    private function writeCaptionFile(string $path, string $audioPath): void
    {
        $script = trim((string) $this->video->script_text);
        if ($script === '') {
            file_put_contents($path, '');
            return;
        }

        $chunks = $this->captionChunks($script);
        $duration = max(8, $this->mediaDuration($audioPath) ?: (float) $this->video->duration_seconds);
        $secondsPerChunk = max(1.2, $duration / max(1, count($chunks)));

        $content = "[Script Info]\n";
        $content .= "ScriptType: v4.00+\n";
        $content .= "PlayResX: 1080\n";
        $content .= "PlayResY: 1920\n\n";
        $content .= "[V4+ Styles]\n";
        $content .= "Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic, Underline, StrikeOut, ScaleX, ScaleY, Spacing, Angle, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, Encoding\n";
        $content .= "Style: Default,DejaVu Sans,58,&H00FFFFFF,&H000000FF,&H00000000,&HAA000000,-1,0,0,0,100,100,0,0,3,4,0,2,88,88,300,1\n\n";
        $content .= "[Events]\n";
        $content .= "Format: Layer, Start, End, Style, Name, MarginL, MarginR, MarginV, Effect, Text\n";

        foreach ($chunks as $index => $chunk) {
            $start = $index * $secondsPerChunk;
            $end = min($duration, $start + $secondsPerChunk);
            $caption = $this->formatCaptionText(implode(' ', $chunk));
            $content .= 'Dialogue: 0,' . $this->assTime($start) . ',' . $this->assTime($end) . ',Default,,0,0,0,,' . $caption . "\n";
        }

        file_put_contents($path, $content);
    }

    private function captionChunks(string $script): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/u', trim($script), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $chunks = [];

        foreach ($sentences as $sentence) {
            $words = preg_split('/\s+/', preg_replace('/[^\pL\pN\' -]+/u', ' ', $sentence), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            foreach (array_chunk($words, 4) as $chunk) {
                $chunks[] = $chunk;
            }
        }

        if (empty($chunks)) {
            $words = preg_split('/\s+/', preg_replace('/[^\pL\pN\' -]+/u', ' ', $script), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $chunks = array_chunk($words, 4);
        }

        return $chunks ?: [[]];
    }

    private function runFfmpeg(
        string $backgroundPath,
        string $audioPath,
        ?string $captionPath,
        string $outputPath,
        bool $withCaptions
    ): bool {
        $videoFilter = 'scale=1080:1920:force_original_aspect_ratio=increase,crop=1080:1920,fps=30';

        if ($withCaptions && $captionPath) {
            $subtitlePath = str_replace('\\', '/', $captionPath);
            $subtitlePath = str_replace([':', "'"], ['\\:', "\\'"], $subtitlePath);
            $videoFilter .= ",ass='{$subtitlePath}'";
        }

        $process = new Process([
            config('evolink.ffmpeg_path', 'ffmpeg'),
            '-y',
            '-stream_loop',
            '-1',
            '-i',
            $backgroundPath,
            '-i',
            $audioPath,
            '-map',
            '0:v:0',
            '-map',
            '1:a:0',
            '-vf',
            $videoFilter,
            '-c:v',
            'libx264',
            '-preset',
            'veryfast',
            '-crf',
            '20',
            '-c:a',
            'aac',
            '-b:a',
            '192k',
            '-shortest',
            '-movflags',
            '+faststart',
            $outputPath,
        ]);

        $process->setTimeout(900);
        $process->run();

        if (! $process->isSuccessful()) {
            if ($withCaptions) {
                Log::warning('FFmpeg caption render failed: ' . $process->getErrorOutput());
                return false;
            }

            throw new RuntimeException('FFmpeg render failed: ' . $process->getErrorOutput());
        }

        return true;
    }

    private function srtTime(float $seconds): string
    {
        $milliseconds = (int) round(($seconds - floor($seconds)) * 1000);
        $totalSeconds = (int) floor($seconds);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $secs = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d,%03d', $hours, $minutes, $secs, $milliseconds);
    }

    private function assTime(float $seconds): string
    {
        $centiseconds = (int) round(($seconds - floor($seconds)) * 100);
        $totalSeconds = (int) floor($seconds);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $secs = $totalSeconds % 60;

        return sprintf('%d:%02d:%02d.%02d', $hours, $minutes, $secs, $centiseconds);
    }

    private function mediaDuration(string $path): ?float
    {
        $process = new Process([
            config('evolink.ffprobe_path', 'ffprobe'),
            '-v',
            'error',
            '-show_entries',
            'format=duration',
            '-of',
            'default=noprint_wrappers=1:nokey=1',
            $path,
        ]);

        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        $duration = (float) trim($process->getOutput());

        return $duration > 0 ? $duration : null;
    }

    private function formatCaptionText(string $caption): string
    {
        $caption = trim(preg_replace('/\s+/u', ' ', $caption));
        $caption = str_replace(['{', '}', "\n", "\r"], '', $caption);
        $caption = mb_strtolower($caption, 'UTF-8');

        return mb_strtoupper(mb_substr($caption, 0, 1, 'UTF-8'), 'UTF-8')
            . mb_substr($caption, 1, null, 'UTF-8');
    }

    private function waitForTask(string $taskId, string $taskName, int $timeout): array
    {
        $startedAt = time();
        $pollInterval = (int) config('evolink.poll_interval', 5);

        do {
            $response = Http::timeout(60)
                ->withToken(config('evolink.api_key'))
                ->get($this->endpoint("/tasks/{$taskId}"));

            if ($response->failed()) {
                throw new RuntimeException("Evolink task polling error ({$taskName}): " . $response->body());
            }

            $task = $response->json() ?? [];
            $status = $task['status'] ?? null;

            if ($status === 'completed') {
                return $task;
            }

            if ($status === 'failed') {
                $message = $task['error']['message'] ?? $task['message'] ?? "Evolink {$taskName} failed.";
                throw new RuntimeException("{$message} Task ID: {$taskId}");
            }

            sleep($pollInterval);
        } while ((time() - $startedAt) < $timeout);

        throw new RuntimeException("Evolink {$taskName} timeout. Task ID: {$taskId}");
    }

    private function extractResultUrl(array $task, array $keys): string
    {
        $result = $task['results'][0] ?? null;

        if (is_string($result) && filter_var($result, FILTER_VALIDATE_URL)) {
            return $result;
        }

        if (is_array($result)) {
            foreach ($keys as $key) {
                if (! empty($result[$key]) && filter_var($result[$key], FILTER_VALIDATE_URL)) {
                    return $result[$key];
                }
            }
        }

        if (isset($task['result_data']) && is_array($task['result_data'])) {
            foreach ($keys as $key) {
                if (! empty($task['result_data'][$key]) && filter_var($task['result_data'][$key], FILTER_VALIDATE_URL)) {
                    return $task['result_data'][$key];
                }
            }
        }

        $resultsStr = json_encode($task);
        if (preg_match('/"(?:url|image_url|file_url)"\s*:\s*"(http[^"]+)"/', $resultsStr, $matches)) {
            return $matches[1];
        }

        throw new RuntimeException('Evolink task completed but did not return a usable URL: ' . json_encode($task));
    }

    private function imageModel(): string
    {
        $model = (string) config('evolink.image_model', 'gemini-3-pro-image-preview');

        return match ($model) {
            'nano-banana-pro-beta', 'nanobanana-pro', 'nano-banana-pro' => 'gemini-3-pro-image-preview',
            default => $model,
        };
    }

    private function endpoint(string $path): string
    {
        return rtrim(config('evolink.base_url'), '/') . '/' . ltrim($path, '/');
    }
}
