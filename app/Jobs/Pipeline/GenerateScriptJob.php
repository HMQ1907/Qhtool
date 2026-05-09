<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GenerateScriptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CampaignVideo $video)
    {
    }

    public function handle(): void
    {
        $this->video->update(['status' => 'generating_script']);

        try {
            $prompt = "Write a short, engaging 30-second TikTok/Shorts video script in the Psychology & Stoicism niche. It must be in English. Make it deep, cinematic, and thought-provoking. No camera directions, just the spoken text.";
            if ($this->video->video_type === 'affiliate') {
                $prompt .= " The script MUST end with a soft-selling call to action, exactly like: 'Check the link in my bio to get the full Stoicism survival guide.'";
            } else {
                $prompt .= " The script MUST end with an engagement hook like: 'Follow for more daily Stoic wisdom.'";
            }

            $response = Http::timeout(120)
                ->withToken(config('evolink.api_key'))
                ->post(config('evolink.base_url') . '/chat/completions', [
                    'model' => config('evolink.text_model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert scriptwriter specializing in the Psychology and Stoicism niches for short-form video.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                throw new \Exception('Evolink API error: ' . $response->body());
            }

            $data = $response->json();
            $scriptStr = $data['choices'][0]['message']['content'] ?? '';
            $scriptStr = trim(str_replace(['"', '*'], '', $scriptStr));
            
            $this->video->update([
                'script_text' => $scriptStr,
                'status' => 'generating_voice'
            ]);

            GenerateVoiceJob::dispatch($this->video);

        } catch (\Exception $e) {
            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
