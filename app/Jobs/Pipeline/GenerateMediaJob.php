<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GenerateMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CampaignVideo $video)
    {
    }

    public function handle(): void
    {
        $this->video->update(['status' => 'generating_media']);

        try {
            // Text to Video using kling-v3-text-to-video.
            // As this is an Async process, it will return a task ID. 
            // In a real production environment, you would save the task_id and have a command/scheduler check the status.
            // For now, we simulate starting it and recording the task info.
            
            $prompt = 'A beautiful cinematic low-angle shot of a roman marble statue crying standing in a snowy forest. Professional lighting, 4k, moody, atmospheric.';

            $response = Http::timeout(120)
                ->withToken(config('evolink.api_key'))
                ->post(config('evolink.base_url') . '/videos/generations', [ // Typical endpoint format, may vary
                    'model' => config('evolink.video_model'),
                    'prompt' => $prompt
                ]);

            if ($response->failed()) {
                throw new \Exception('Evolink API error (Media): ' . $response->body());
            }

            // In actual usage, this returns a task ID to query later.
            // We just store a mock URL for now to let the pipeline proceed in testing, 
            // or we could save the task ID in DB to poll later.
            $data = $response->json();
            $taskId = $data['id'] ?? null;
            
            // Assume the API provides a URL immediately or we poll. 
            // For the sake of the skeleton, we advance to render with a dummy video URL.
            $videoBgUrl = "storage/app/public/backgrounds/dummy_bg_".$this->video->id.".mp4";
            
            $this->video->update([
                'background_video_url' => $videoBgUrl,
                'status' => 'rendering'
            ]);

            RenderFinalVideoJob::dispatch($this->video);

        } catch (\Exception $e) {
            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
