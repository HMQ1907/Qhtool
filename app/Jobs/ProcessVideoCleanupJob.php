<?php

namespace App\Jobs;

use App\Models\VideoCleanup;
use App\Services\VideoCleanupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProcessVideoCleanupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 1200;

    public function __construct(
        private readonly VideoCleanup $videoCleanup,
    ) {}

    public function handle(VideoCleanupService $videoCleanupService): void
    {
        Log::info('[ProcessVideoCleanupJob] Start', ['id' => $this->videoCleanup->id]);

        try {
            $this->videoCleanup->markAsProcessing();

            $outputRelativePath = 'cleaned/videos/video_cleanup_' . $this->videoCleanup->id . '_' . Str::random(8) . '.mp4';

            $savedPath = $videoCleanupService->cleanupSubtitle(
                $this->videoCleanup->input_video_path,
                $outputRelativePath,
                [
                    'left_pct' => $this->videoCleanup->left_pct,
                    'top_pct' => $this->videoCleanup->top_pct,
                    'width_pct' => $this->videoCleanup->width_pct,
                    'height_pct' => $this->videoCleanup->height_pct,
                ]
            );

            $this->videoCleanup->markAsDone($savedPath);

            Log::info('[ProcessVideoCleanupJob] Done', [
                'id' => $this->videoCleanup->id,
                'output_path' => $savedPath,
            ]);
        } catch (Throwable $exception) {
            Log::error('[ProcessVideoCleanupJob] Failed', [
                'id' => $this->videoCleanup->id,
                'error' => $exception->getMessage(),
            ]);

            $this->videoCleanup->markAsFailed($exception->getMessage());
        }
    }
}
