<?php

namespace App\Services\AI;

use RuntimeException;

class EvoLinkVideoService
{
    public function __construct(
        private readonly EvoLinkClient $client,
    ) {}

    public function buildPrompt(string $sourceTitle, string $animationLabel, ?string $userPrompt = null): string
    {
        $base = "Create a polished fashion marketing video inspired by {$sourceTitle}, "
              . "with {$animationLabel} motion, smooth camera movement, premium lighting, "
              . "cinematic product showcase, and social commerce ready composition";

        if (!empty($userPrompt)) {
            $base .= ', ' . $userPrompt;
        }

        return $base;
    }

    public function generateVideo(
        string $prompt,
        string $filename,
        ?int $duration = null,
        ?string $aspectRatio = null,
        ?string $quality = null,
        ?string $sound = null
    ): string {
        $task = $this->client->createVideoTask([
            'model' => config('ai.evolink.video_model', 'kling-v3-text-to-video'),
            'prompt' => $prompt,
            'duration' => $duration ?? (int) config('ai.evolink.video_duration', 5),
            'aspect_ratio' => $aspectRatio ?? config('ai.evolink.video_aspect_ratio', '16:9'),
            'quality' => $quality ?? config('ai.evolink.video_quality', '720p'),
            'sound' => $sound ?? config('ai.evolink.video_sound', 'off'),
        ]);

        $taskId = $task['id'] ?? null;

        if (empty($taskId)) {
            throw new RuntimeException('EvoLink không trả về task ID cho video');
        }

        $taskDetail = $this->client->waitForTask(
            $taskId,
            (int) config('ai.evolink.video_timeout', 420),
            (int) config('ai.evolink.poll_interval', 4)
        );

        $videoUrl = $taskDetail['results'][0] ?? null;

        if (empty($videoUrl)) {
            throw new RuntimeException('EvoLink không trả về video kết quả');
        }

        return $this->client->downloadResult($videoUrl, $filename, 'generated/videos', 'mp4');
    }
}