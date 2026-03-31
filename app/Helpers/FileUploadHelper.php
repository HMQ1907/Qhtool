<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Throwable;

class FileUploadHelper
{
    private const UPLOAD_PATH = 'images/purchase-templates/';

    public static function handleFileUpload(
        $file,
        string $prefix,
        int $width = 300,
        int $height = 300,
        int $quality = 80
    ): ?string {
        try {
            if ($file == 'Webike_Thailand.jpg') {
                return '/' . self::UPLOAD_PATH . 'Webike_Thailand.jpg';
            }

            if (!$file instanceof UploadedFile) {
                return is_string($file) ? $file : null;
            }

            $filename = $prefix . '_' . time() . '.' . $file->getClientOriginalExtension();
            $publicPath = public_path(self::UPLOAD_PATH);

            @mkdir($publicPath, 0755, true);

            $image = Image::read($file);

            if ($prefix === 'webike_logo' && $image->height() > 150) {
                $image->resize(null, null, fn($c) => $c->aspectRatio()->upsize());
            } else {
                $image->resize($width, $height, fn($c) => $c->aspectRatio()->upsize());
            }

            file_put_contents("$publicPath$filename", $image->encodeByExtension($file->getClientOriginalExtension(), quality: $quality));

            return '/' . self::UPLOAD_PATH . $filename;
        } catch (Throwable $th) {
            Log::error(__METHOD__, [
                'file' => $file,
                'prefix' => $prefix
            ]);

            throw $th;
        }
    }

    public static function deleteOldFile(?string $filePath): bool
    {
        if (!$filePath) {
            return true;
        }

        $files = explode('/', $filePath);
        $fileName = end($files);

        if ($fileName == 'Webike_Thailand.jpg') {
            return true;
        }

        return File::delete(public_path($filePath));
    }
}
