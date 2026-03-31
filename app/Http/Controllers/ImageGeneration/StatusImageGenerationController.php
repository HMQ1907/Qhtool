<?php

namespace App\Http\Controllers\ImageGeneration;

use App\Models\GeneratedImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class StatusImageGenerationController extends ImageGenerationController
{
    public function __invoke(int $id): JsonResponse
    {
        $generatedImage = GeneratedImage::findOrFail($id);

        $response = [
            'id'            => $generatedImage->id,
            'status'        => $generatedImage->status,
            'error_message' => $generatedImage->error_message,
            'output_url'    => null,
        ];

        if ($generatedImage->isDone() && $generatedImage->output_image_path) {
            $response['output_url'] = Storage::url($generatedImage->output_image_path);
        }

        return response()->json($response);
    }
}