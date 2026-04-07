<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseStorageService
{
    public function publicUrl(string $objectPath): string
    {
        $bucket = (string) config('services.supabase.bucket', '');
        $supabaseUrl = rtrim((string) config('services.supabase.url', ''), '/');
        $publicUrlBase = rtrim((string) config('services.supabase.public_url', $supabaseUrl), '/');

        $encodedObjectPath = implode('/', array_map('rawurlencode', explode('/', ltrim($objectPath, '/'))));

        return "{$publicUrlBase}/storage/v1/object/public/{$bucket}/{$encodedObjectPath}";
    }

    public function uploadPublicFile(string $sourcePath, string $folder, string $filename): array
    {
        $bucket = (string) config('services.supabase.bucket', '');
        $supabaseUrl = rtrim((string) config('services.supabase.url', ''), '/');
        $serviceRoleKey = (string) config('services.supabase.service_role_key', '');
        $publicUrlBase = rtrim((string) config('services.supabase.public_url', $supabaseUrl), '/');

        if ($bucket === '' || $supabaseUrl === '' || $serviceRoleKey === '') {
            throw new RuntimeException('Thiếu cấu hình Supabase Storage trong .env');
        }

        if (!is_file($sourcePath)) {
            throw new RuntimeException("Không tìm thấy file để upload: {$sourcePath}");
        }

        $contents = file_get_contents($sourcePath);

        if ($contents === false) {
            throw new RuntimeException("Không thể đọc file để upload: {$sourcePath}");
        }

        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $mimeType = mime_content_type($sourcePath) ?: 'application/octet-stream';
        $objectPath = trim($folder, '/') . '/' . $filename . ($extension !== '' ? '.' . $extension : '');
        $encodedObjectPath = implode('/', array_map('rawurlencode', explode('/', $objectPath)));

        $response = Http::withToken($serviceRoleKey)
            ->withHeaders([
                'Content-Type' => $mimeType,
                'x-upsert' => 'true',
            ])
            ->withBody($contents, $mimeType)
            ->timeout(60)
            ->put("{$supabaseUrl}/storage/v1/object/{$bucket}/{$encodedObjectPath}");

        if ($response->failed()) {
            throw new RuntimeException('Không thể upload file lên Supabase Storage: ' . $response->body());
        }

        return [
            'path' => $objectPath,
            'url' => "{$publicUrlBase}/storage/v1/object/public/{$bucket}/{$encodedObjectPath}",
        ];
    }

    public function deleteObject(string $objectPath): void
    {
        $bucket = (string) config('services.supabase.bucket', '');
        $supabaseUrl = rtrim((string) config('services.supabase.url', ''), '/');
        $serviceRoleKey = (string) config('services.supabase.service_role_key', '');

        if ($bucket === '' || $supabaseUrl === '' || $serviceRoleKey === '') {
            throw new RuntimeException('Thiếu cấu hình Supabase Storage trong .env');
        }

        $encodedObjectPath = implode('/', array_map('rawurlencode', explode('/', ltrim($objectPath, '/'))));

        $response = Http::withToken($serviceRoleKey)
            ->timeout(60)
            ->delete("{$supabaseUrl}/storage/v1/object/{$bucket}/{$encodedObjectPath}");

        if ($response->failed()) {
            throw new RuntimeException('Không thể xóa file trên Supabase Storage: ' . $response->body());
        }
    }
}
