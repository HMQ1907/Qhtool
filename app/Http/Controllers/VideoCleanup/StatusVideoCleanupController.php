<?php

namespace App\Http\Controllers\VideoCleanup;

use App\Http\Controllers\Controller;
use App\Models\VideoCleanup;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class StatusVideoCleanupController extends Controller
{
    public function __invoke(int $id): JsonResponse
    {
        $videoCleanup = VideoCleanup::findOrFail($id);

        return response()->json([
            'id' => $videoCleanup->id,
            'status' => $videoCleanup->status,
            'error_message' => $videoCleanup->error_message,
            'output_url' => $videoCleanup->isDone() && $videoCleanup->output_video_path
                ? Storage::url($videoCleanup->output_video_path)
                : null,
        ]);
    }
}
