<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateVoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CampaignVideo $video)
    {
    }

    public function handle(): void
    {
        $this->video->update(['status' => 'generating_voice']);

        try {
            $response = Http::timeout(120)
                ->withToken(config('evolink.api_key'))
                ->post(config('evolink.base_url') . '/audio/speech', [
                    'model' => config('evolink.voice_model'),
                    'input' => $this->video->script_text,
                    'voice' => 'default_custom_voice' // Note: This normally requires a custom voice created via qwen-voice-design
                ]);

            if ($response->failed()) {
                throw new \Exception('Evolink API error (Voice): ' . $response->body());
            }

            // Save audio to disk
            $filename = 'voices/' . Str::uuid() . '.mp3';
            Storage::disk('public')->put($filename, $response->body());
            
            $this->video->update([
                'voice_audio_url' => Storage::url($filename),
                'status' => 'generating_media'
            ]);

            GenerateMediaJob::dispatch($this->video);

        } catch (\Exception $e) {
            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
