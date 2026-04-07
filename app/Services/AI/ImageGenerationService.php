<?php

namespace App\Services\AI;

class ImageGenerationService
{
    public function __construct(
        private readonly EvoLinkImageService $evoLinkImageService,
    ) {}

    public function buildPrompt(string $modelName, string $bgName, ?string $userPrompt = null): string
    {
        return $this->evoLinkImageService->buildPrompt($modelName, $bgName, $userPrompt);
    }

    public function generateImage(
        string $prompt,
        string $sourceImageUrl,
        string $modelImageUrl,
        string $backgroundImageUrl,
        string $filename
    ): string {
        return $this->evoLinkImageService->generateImage(
            $prompt,
            $sourceImageUrl,
            $modelImageUrl,
            $backgroundImageUrl,
            $filename,
        );
    }
}