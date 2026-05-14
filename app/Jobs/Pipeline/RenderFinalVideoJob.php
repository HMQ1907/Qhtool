<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RenderFinalVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CampaignVideo $video) {}

    public function handle(): void
    {
        $this->video->update(['status' => 'rendering']);

        try {
            if (empty($this->video->background_video_url)) {
                throw new RuntimeException('Missing EvoLink cloud video URL.');
            }

            $this->video->update([
                'final_video_url' => $this->video->background_video_url,
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
        } catch (\Exception $e) {
            Log::error('Render Final Video Error: ' . $e->getMessage());

            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->video->campaign->update(['status' => 'failed']);
        }
    }
}
