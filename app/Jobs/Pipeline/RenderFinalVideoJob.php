<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RenderFinalVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CampaignVideo $video)
    {
    }

    public function handle(): void
    {
        $this->video->update(['status' => 'rendering']);

        try {
            // TODO: Dùng thư viện FFmpeg trộn $video->background_video_url và $video->voice_audio_url
            // Cắt background cho bằng loop của audio.
            // Nếu video_type === 'affiliate', dùng thư viện crop overlay hoặc dán sticker/watermark mui tên trỏ lên avatar.
            
            // Giả lập kết quả
            $finalUrl = "storage/app/public/final_videos/output_".$this->video->id.".mp4";
            
            $this->video->update([
                'final_video_url' => $finalUrl,
                'status' => 'completed'
            ]);
            
            // Cập nhật status của campaign nếu toàn bộ video đã xong
            $campaign = $this->video->campaign;
            $allDone = $campaign->videos()
                      ->whereNotIn('status', ['completed', 'failed'])
                      ->count() === 0;
            
            if ($allDone) {
                $campaign->update(['status' => 'completed']);
            }

        } catch (\Exception $e) {
            Log::error('Render Final Video Error: ' . $e->getMessage());
            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            $this->video->campaign->update(['status' => 'failed']);
        }
    }
}
