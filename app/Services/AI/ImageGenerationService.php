<?php

namespace App\Services\AI;

use RuntimeException;

class ImageGenerationService
{
    public function __construct(
        private readonly FalImageService $falImageService,
        private readonly HuggingFaceImageService $huggingFaceImageService,
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
        string $productImageUrl,
        string $modelImageUrl,
        string $filename
    ): string {
        $provider = strtolower((string) config('ai.provider', 'fal'));

        return match ($provider) {
            'huggingface', 'hf' => $this->huggingFaceImageService->generateAndSaveFromImage($sourceImagePath, $prompt, $filename),
            'fal' => $this->generateWithFal($prompt, $productImageUrl, $modelImageUrl, $filename),
            default => throw new RuntimeException("AI provider không hợp lệ: {$provider}"),
        };
    }

    private function generateWithFal(
        string $prompt,
        string $productImageUrl,
        string $modelImageUrl,
        string $filename
    ): string {
        $resultImageUrl = $this->falImageService->generateImage($prompt, $productImageUrl, $modelImageUrl);

        return $this->falImageService->downloadAndSave($resultImageUrl, $filename);
    }
}