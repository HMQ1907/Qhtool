<?php

namespace App\Http\Controllers\ImageGeneration;

use App\Http\Controllers\Controller;
use App\Models\GeneratedImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

abstract class ImageGenerationController extends Controller
{
    /**
     * Lấy danh sách ảnh từ thư mục public.
     */
    protected function getImageList(string $directory, string $urlPrefix): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        $files = array_values(array_filter(scandir($directory) ?: [], function ($file) use ($directory, $allowedExtensions) {
            if ($file === '.' || $file === '..') {
                return false;
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $file;

            if (!is_file($filePath)) {
                return false;
            }

            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedExtensions, true);
        }));

        return collect($files)->map(function ($file) use ($urlPrefix) {
            $filename = basename($file);

            return [
                'name' => pathinfo($filename, PATHINFO_FILENAME),
                'url'  => "{$urlPrefix}/{$filename}",
                'path' => "{$urlPrefix}/{$filename}",
            ];
        })->values()->toArray();
    }

    /**
     * Trả về các ảnh đã tạo gần đây của user hiện tại.
     */
    protected function getGeneratedImages(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return GeneratedImage::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('output_image_path')
            ->latest()
            ->limit(8)
            ->get()
            ->map(function (GeneratedImage $image) {
                return [
                    'id' => $image->id,
                    'url' => Storage::url($image->output_image_path),
                    'status' => $image->status,
                    'created_at' => $image->created_at?->format('d/m H:i'),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Validate đường dẫn asset không chứa path traversal.
     */
    protected function validateAssetPath(string $path, string $expectedPrefix, string $fieldName): void
    {
        $realPath     = realpath(public_path(ltrim($path, '/')));
        $expectedBase = realpath(public_path($expectedPrefix));

        if (!$realPath || !$expectedBase || !str_starts_with($realPath, $expectedBase)) {
            throw ValidationException::withMessages([
                $fieldName => 'Đường dẫn ảnh không hợp lệ.',
            ]);
        }
    }
}