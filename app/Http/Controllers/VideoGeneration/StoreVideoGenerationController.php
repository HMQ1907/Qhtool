<?php

namespace App\Http\Controllers\VideoGeneration;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateVideoJob;
use App\Models\GeneratedVideo;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreVideoGenerationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_image_url' => ['required', 'string', 'max:2048'],
            'source_image_title' => ['nullable', 'string', 'max:255'],
            'animation' => ['required', 'string', 'max:50'],
            'prompt' => ['nullable', 'string', 'max:500'],
            'duration' => ['nullable', 'integer', 'min:3', 'max:15'],
            'aspect_ratio' => ['nullable', 'in:16:9,9:16,1:1'],
            'quality' => ['nullable', 'in:720p,1080p'],
            'sound' => ['nullable', 'in:on,off'],
        ], [
            'source_image_url.required' => 'Vui lòng chọn ảnh nguồn để tạo video.',
            'animation.required' => 'Vui lòng chọn kiểu chuyển động.',
        ]);

        $user = Auth::user();

        if ($user->role !== 'admin' && $user->free_videos_left <= 0) {
            return response()->json([
                'message' => 'Bạn đã sử dụng hết lượt tạo video miễn phí. Vui lòng nâng cấp tài khoản.',
            ], 403);
        }

        $generatedVideo = GeneratedVideo::create([
            'user_id' => Auth::id(),
            'source_image_url' => $validated['source_image_url'],
            'source_image_title' => $validated['source_image_title'] ?? null,
            'prompt' => $validated['prompt'] ?? null,
            'animation' => $validated['animation'],
            'duration' => $validated['duration'] ?? 5,
            'aspect_ratio' => $validated['aspect_ratio'] ?? '16:9',
            'quality' => $validated['quality'] ?? '720p',
            'sound' => $validated['sound'] ?? 'off',
            'model' => config('ai.evolink.video_model', 'kling-v3-text-to-video'),
            'status' => GeneratedVideo::STATUS_PENDING,
        ]);

        GenerateVideoJob::dispatch($generatedVideo);

        if ($user->role !== 'admin') {
            User::query()->whereKey(Auth::id())->decrement('free_videos_left');
        }

        return response()->json([
            'id' => $generatedVideo->id,
            'status' => $generatedVideo->status,
            'message' => 'Yêu cầu tạo video đã được gửi. Đang xử lý...',
        ], 202);
    }
}