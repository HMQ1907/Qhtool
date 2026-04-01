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
        string $sourceImagePath,
        string $modelImagePath,
        string $backgroundImagePath,
        string $filename
    ): string {
        return $this->evoLinkImageService->generateImage(
            $prompt,
            $sourceImagePath,
            $modelImagePath,
            $backgroundImagePath,
            $filename,
        );
    }
}