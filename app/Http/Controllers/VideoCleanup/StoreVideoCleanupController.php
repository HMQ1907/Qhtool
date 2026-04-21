<?php

namespace App\Http\Controllers\VideoCleanup;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoCleanupJob;
use App\Models\VideoCleanup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreVideoCleanupController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $defaults = config('video_cleanup.default_region');

        $validated = $request->validate([
            'video' => ['required', 'file', 'max:102400', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo,video/x-matroska'],
            'left_pct' => ['nullable', 'numeric', 'min:0', 'max:95'],
            'top_pct' => ['nullable', 'numeric', 'min:0', 'max:95'],
            'width_pct' => ['nullable', 'numeric', 'gt:0', 'max:100'],
            'height_pct' => ['nullable', 'numeric', 'gt:0', 'max:100'],
        ], [
            'video.required' => 'Vui long upload video can xoa subtitle.',
            'video.max' => 'Video khong duoc vuot qua 100MB.',
            'video.mimetypes' => 'Chi ho tro mp4, mov, webm, avi, mkv.',
        ]);

        $inputVideoPath = $request->file('video')->store('uploads/videos', 'public');

        $videoCleanup = VideoCleanup::create([
            'user_id' => Auth::id(),
            'input_video_path' => $inputVideoPath,
            'input_original_name' => $request->file('video')->getClientOriginalName(),
            'left_pct' => $validated['left_pct'] ?? $defaults['left_pct'],
            'top_pct' => $validated['top_pct'] ?? $defaults['top_pct'],
            'width_pct' => $validated['width_pct'] ?? $defaults['width_pct'],
            'height_pct' => $validated['height_pct'] ?? $defaults['height_pct'],
            'status' => VideoCleanup::STATUS_PENDING,
        ]);

        ProcessVideoCleanupJob::dispatch($videoCleanup);

        return response()->json([
            'id' => $videoCleanup->id,
            'status' => $videoCleanup->status,
            'message' => 'Da nhan video. Dang xoa subtitle...',
        ], 202);
    }
}
