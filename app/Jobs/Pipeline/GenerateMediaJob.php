<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Support\AffiliateCreativeBrief;
use App\Support\ShortVideoCreativeBrief;
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
            $duration = $this->normalizeDuration(
                (int) ($this->video->duration_seconds ?: config('evolink.monetization_duration', 15))
            );
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
            throw $e; // Quăng lỗi ra terminal
        }
    }

    private function buildVideoPrompt(int $duration): string
    {
        if ($this->video->video_type === 'affiliate') {
            $brief = AffiliateCreativeBrief::forVariation(
                (string) ($this->video->generation_mode ?: 'fast_test'),
                max(0, (int) $this->video->id)
            );

            return implode(', ', [
                "A {$duration}-second seamless cinematic vertical 9:16 TikTok Shop affiliate product B-roll sequence",
                "Product: {$this->video->product_name}",
                "Product notes: " . ($this->video->product_description ?: 'use only safe visible context, do not invent specific claims'),
                "Buyer pain point: " . ($this->video->product_pain_points ?: $brief['angle']),
                "Creative mode: {$brief['mode_label']}, {$brief['goal']}",
                "Visual style: {$brief['visual_style']}",
                "Shot language: product close-ups, practical use-case, before-after feeling, clear hero product framing, natural TikTok review pacing",
                "Style: photorealistic, believable, high detail, social commerce ad, attractive but not overproduced",
                "Absolutely no logos, no watermarks, no subtitles, no readable text, no letters, no UI elements, no fake hands, no people unless the source product clearly requires scale context",
            ]);
        }

        $brief = ShortVideoCreativeBrief::for($this->video);
        $tone = $this->video->video_type === 'affiliate'
            ? 'mysterious, persuasive, premium'
            : 'profound, dark academia, motivational';

        return implode(', ', [
            "A {$duration}-second seamless cinematic vertical 9:16 B-roll sequence for a premium Stoicism and Psychology Shorts channel",
            "Episode theme: {$brief['series']} about {$brief['angle']}",
            "Visual world: {$brief['visual_world']}",
            "Signature symbol: {$brief['symbol']}",
            "Camera language: {$brief['motion']}",
            "Color palette: {$brief['palette']}",
            "Style: photorealistic, high detail, dramatic chiaroscuro lighting, restrained, intelligent, {$tone}",
            "Keep a consistent premium channel identity: ancient philosophy, modern psychological tension, calm power",
            "Absolutely no logos, no watermarks, no subtitles, no readable text, no letters, no UI elements, clean composition"
        ]);
    }

    private function normalizeDuration(int $duration): int
    {
        $maxDuration = (int) config('evolink.max_video_duration', 15);

        return max(1, min($duration, $maxDuration));
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
        
        // Dự phòng như bên Voice: API có thể trả về result_data
        if (isset($task['result_data'])) {
            foreach (['video_url', 'url', 'file_url'] as $key) {
                if (! empty($task['result_data'][$key]) && filter_var($task['result_data'][$key], FILTER_VALIDATE_URL)) {
                    return $task['result_data'][$key];
                }
            }
        }

        // Dự phòng 2: Regex quét toàn bộ JSON giống hệt bên Voice
        $resultsStr = json_encode($task);
        if (preg_match("/\"(?:video_url|url|file_url)\"\s*:\s*\"(http[^\"]+)\"/", $resultsStr, $matches)) {
            return $matches[1];
        }

        throw new RuntimeException('Evolink completed the task but did not return a video URL: ' . json_encode($task));
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
