<?php

namespace App\Http\Controllers\ImageGeneration;

use App\Jobs\GenerateImageJob;
use App\Models\GeneratedImage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreImageGenerationController extends ImageGenerationController
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_image' => ['required', 'image', 'max:10240', 'mimes:jpg,jpeg,png,webp'],
            'model_path'    => ['required', 'string'],
            'bg_path'       => ['required', 'string'],
            'prompt'        => ['nullable', 'string', 'max:500'],
        ], [
            'product_image.required' => 'Vui lòng upload ảnh sản phẩm.',
            'product_image.image'    => 'File phải là ảnh (jpg, png, webp).',
            'product_image.max'      => 'Ảnh không được vượt quá 10MB.',
            'model_path.required'    => 'Vui lòng chọn người mẫu.',
            'bg_path.required'       => 'Vui lòng chọn background.',
        ]);

        $user = Auth::user();

        if ($user->role !== 'admin' && $user->free_images_left <= 0) {
            return response()->json([
                'message' => 'Bạn đã sử dụng hết lượt tạo ảnh miễn phí. Vui lòng nâng cấp tài khoản.',
            ], 403);
        }

        $this->validateAssetPath($validated['model_path'], 'images/models', 'model_path');
        $this->validateAssetPath($validated['bg_path'], 'images/background', 'bg_path');

        $inputImagePath = $request->file('product_image')->store('uploads', 'public');

        $generatedImage = GeneratedImage::create([
            'user_id'          => Auth::id(),
            'input_image_path' => $inputImagePath,
            'model_path'       => $validated['model_path'],
            'background_path'  => $validated['bg_path'],
            'prompt'           => $validated['prompt'],
            'status'           => GeneratedImage::STATUS_PENDING,
        ]);

        GenerateImageJob::dispatch($generatedImage);

        if ($user->role !== 'admin') {
            User::query()->whereKey(Auth::id())->decrement('free_images_left');
        }

        return response()->json([
            'id'      => $generatedImage->id,
            'status'  => $generatedImage->status,
            'message' => 'Yêu cầu đã được gửi. Đang xử lý...',
        ], 202);
    }
}