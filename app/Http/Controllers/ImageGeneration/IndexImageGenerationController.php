<?php

namespace App\Http\Controllers\ImageGeneration;

use Inertia\Inertia;
use Inertia\Response;

class IndexImageGenerationController extends ImageGenerationController
{
    public function __invoke(): Response
    {
        return Inertia::render('ImageGenerator', [
            'models'          => $this->getImageList(public_path('images/models'), '/images/models'),
            'backgrounds'     => $this->getImageList(public_path('images/background'), '/images/background'),
            'generatedImages' => $this->getGeneratedImages(),
        ]);
    }
}