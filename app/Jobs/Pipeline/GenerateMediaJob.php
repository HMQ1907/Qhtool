<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GenerateMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

    public function __construct(public CampaignVideo $video) {}

    public function handle(): void
    {
        $this->video->update(['status' => 'generating_media']);

        try {
            $duration = (int) ($this->video->duration_seconds ?: config('evolink.monetization_duration', 30));
            $aspectRatio = $this->video->aspect_ratio ?: config('evolink.video_aspect_ratio', '9:16');
            $quality = $this->video->quality ?: config('evolink.video_quality', '720p');

            $response = Http::timeout(120)
                ->withToken(config('evolink.api_key'))
                ->post($this->endpoint('/videos/generations'), [
                    'model' => config('evolink.video_model'),
                    'prompt' => $this->buildVideoPrompt($duration),
                    'duration' => $duration,
                    'aspect_ratio' => $aspectRatio,
                    'quality' => $quality,
                    'sound' => config('evolink.video_sound', 'off'),
                ]);

            if ($response->failed()) {
                throw new RuntimeException('Evolink API error (Media): ' . $response->body());
            }

            $taskId = $response->json('id');

            if (empty($taskId)) {
                throw new RuntimeException('Evolink did not return a video task ID.');
            }

            $this->video->update(['external_task_id' => $taskId]);

            $task = $this->waitForTask($taskId);
            $videoUrl = $this->extractResultUrl($task);

            $this->video->update([
                'background_video_url' => $videoUrl,
                'external_url_expires_at' => now()->addDay(),
                'status' => 'rendering',
            ]);

            RenderFinalVideoJob::dispatch($this->video);
        } catch (\Exception $e) {
            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->markCampaignFailedIfNeeded();
        }
    }

    private function buildVideoPrompt(int $duration): string
    {
        $script = trim((string) $this->video->script_text);
        $tone = $this->video->video_type === 'affiliate'
            ? 'subtle persuasive self-improvement ad, no hard sell'
            : 'high-retention motivational wisdom content';

        return implode(' ', [
            "Create a {$duration}-second vertical 9:16 video for YouTube Shorts and Facebook Reels.",
            "Theme: Psychology and Stoicism.",
            "Tone: {$tone}.",
            "Visual style: cinematic, premium, emotional, slow camera movement, dramatic lighting, clean composition.",
            "Avoid logos, watermarks, UI text, unreadable text, and random captions.",
            "The visuals should match this spoken script:",
            $script,
        ]);
    }

    private function waitForTask(string $taskId): array
    {
        $startedAt = time();
        $timeout = (int) config('evolink.video_timeout', 900);
        $pollInterval = (int) config('evolink.poll_interval', 5);

        do {
            $response = Http::timeout(60)
                ->withToken(config('evolink.api_key'))
                ->get($this->endpoint("/tasks/{$taskId}"));

            if ($response->failed()) {
                throw new RuntimeException('Evolink task polling error: ' . $response->body());
            }

            $task = $response->json() ?? [];
            $status = $task['status'] ?? null;

            if ($status === 'completed') {
                return $task;
            }

            if ($status === 'failed') {
                $message = $task['error']['message'] ?? $task['message'] ?? 'Evolink video task failed.';
                throw new RuntimeException($message);
            }

            sleep($pollInterval);
        } while ((time() - $startedAt) < $timeout);

        throw new RuntimeException('Evolink video task timeout.');
    }

    private function extractResultUrl(array $task): string
    {
        $result = $task['results'][0] ?? null;

        if (is_string($result) && filter_var($result, FILTER_VALIDATE_URL)) {
            return $result;
        }

        if (is_array($result)) {
            foreach (['url', 'video_url', 'file_url'] as $key) {
                if (! empty($result[$key]) && filter_var($result[$key], FILTER_VALIDATE_URL)) {
                    return $result[$key];
                }
            }
        }

        throw new RuntimeException('Evolink completed the task but did not return a video URL.');
    }

    private function endpoint(string $path): string
    {
        return rtrim(config('evolink.base_url'), '/') . '/' . ltrim($path, '/');
    }

    private function markCampaignFailedIfNeeded(): void
    {
        $campaign = $this->video->campaign;

        if ($campaign->videos()->where('status', 'failed')->exists()) {
            $campaign->update(['status' => 'failed']);
        }
    }
}
