<?php

namespace App\Services\AI;

use RuntimeException;

class EvoLinkImageService
{
    public function __construct(
        private readonly EvoLinkClient $client,
    ) {}

    public function buildPrompt(string $modelName, string $bgName, ?string $userPrompt = null): string
    {
        $base = "A stylish young {$modelName} model wearing the product clothing, "
              . "in a {$bgName} setting, "
              . "soft natural lighting, professional fashion photography, "
              . "Gen Z aesthetic, high resolution, clean background, "
              . "editorial style, Vietnamese fashion brand look";

        if (!empty($userPrompt)) {
            $base .= ', ' . $userPrompt;
        }

        return $base;
    }

    public function generateImage(
        string $prompt,
        string $productImageUrl,
        string $modelImageUrl,
        string $filename
    ): string {
        $task = $this->client->createImageTask([
            'model' => config('ai.evolink.image_model', 'nano-banana-pro'),
            'prompt' => $prompt,
            'image_urls' => array_values(array_filter([$productImageUrl, $modelImageUrl])),
            'quality' => config('ai.evolink.image_quality', '2K'),
        ]);

        $taskId = $task['id'] ?? null;

        if (empty($taskId)) {
            throw new RuntimeException('EvoLink không trả về task ID cho ảnh');
        }

        $taskDetail = $this->client->waitForTask(
            $taskId,
            (int) config('ai.evolink.image_timeout', 360),
            (int) config('ai.evolink.poll_interval', 4)
        );

        $imageUrl = $taskDetail['results'][0] ?? null;

        if (empty($imageUrl)) {
            throw new RuntimeException('EvoLink không trả về ảnh kết quả');
        }

        return $this->client->downloadResult($imageUrl, $filename, 'generated/images', 'jpg');
    }
}