<?php

namespace App\Http\Controllers\VideoGeneration;

use App\Http\Controllers\Controller;
use App\Models\GeneratedVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class StatusVideoGenerationController extends Controller
{
    public function __invoke(int $id): JsonResponse
    {
        $generatedVideo = GeneratedVideo::findOrFail($id);

        $response = [
            'id' => $generatedVideo->id,
            'status' => $generatedVideo->status,
            'error_message' => $generatedVideo->error_message,
            'output_url' => null,
        ];

        if ($generatedVideo->isDone() && $generatedVideo->output_video_path) {
            $response['output_url'] = Storage::url($generatedVideo->output_video_path);
        }

        return response()->json($response);
    }
}