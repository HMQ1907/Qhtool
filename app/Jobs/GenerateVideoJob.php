<?php

namespace App\Jobs;

use App\Models\GeneratedVideo;
use App\Models\User;
use App\Services\AI\EvoLinkVideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GenerateVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 600;

    /** @var GeneratedVideo */
    private $generatedVideo;

    public function __construct(GeneratedVideo $generatedVideo)
    {
        $this->generatedVideo = $generatedVideo;
    }

    public function handle(EvoLinkVideoService $videoService): void
    {
        Log::info("[GenerateVideoJob] Bắt đầu xử lý ID: {$this->generatedVideo->id}");

        try {
            $this->generatedVideo->markAsProcessing();

            $animationMap = [
                'quay-nhe' => 'gentle rotation',
                'zoom' => 'smooth zoom',
                'catwalk' => 'runway catwalk motion',
            ];

            $animationLabel = $animationMap[$this->generatedVideo->animation ?? 'quay-nhe'] ?? 'gentle motion';
            $sourceTitle = $this->generatedVideo->source_image_title ?: 'the selected fashion image';

            $prompt = $videoService->buildPrompt($sourceTitle, $animationLabel, $this->generatedVideo->prompt);

            Log::info('[GenerateVideoJob] Prompt đã build', ['prompt' => $prompt]);

            $filename = 'video_' . $this->generatedVideo->id . '_' . Str::random(8);
            $savedPath = $videoService->generateVideo(
                $prompt,
                $filename,
                $this->generatedVideo->duration,
                $this->generatedVideo->aspect_ratio,
                $this->generatedVideo->quality,
                $this->generatedVideo->sound
            );

            $this->generatedVideo->markAsDone($savedPath);

            Log::info("[GenerateVideoJob] Hoàn thành ID: {$this->generatedVideo->id}", [
                'output_path' => $savedPath,
            ]);
        } catch (Throwable $e) {
            Log::error("[GenerateVideoJob] Thất bại ID: {$this->generatedVideo->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->generatedVideo->markAsFailed("Lỗi xử lý: {$e->getMessage()}");
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical("[GenerateVideoJob] Job thất bại hoàn toàn ID: {$this->generatedVideo->id}", [
            'error' => $exception->getMessage(),
        ]);

        $this->generatedVideo->markAsFailed('Hệ thống không thể xử lý yêu cầu này. Vui lòng thử lại sau.');

        $user = User::find($this->generatedVideo->user_id);

        if ($user && $user->role !== 'admin') {
            $user->increment('free_videos_left');
        }
    }
}