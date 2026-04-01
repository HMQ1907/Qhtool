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
        string $sourceImagePath,
        string $modelImagePath,
        string $backgroundImagePath,
        string $filename
    ): string {
        $task = $this->client->createImageTask([
            'model' => config('ai.evolink.image_model', 'gemini-3.1-flash-image-preview'),
            'prompt' => $prompt,
            'image_urls' => array_values(array_filter([
                $this->toDataUri($sourceImagePath),
                $this->toDataUri($modelImagePath),
                $this->toDataUri($backgroundImagePath),
            ])),
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

    private function toDataUri(string $path): string
    {
        if (!is_file($path)) {
            throw new RuntimeException("Không tìm thấy file ảnh: {$path}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Không thể đọc file ảnh: {$path}");
        }

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode($contents));
    }
}