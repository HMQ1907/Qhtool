<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HuggingFaceImageService
{
    private const BASE_URL = 'https://router.huggingface.co/';

    private string $token;

    private string $model;

    private string $provider;

    private string $providerModel;

    private int $timeout;

    public function __construct()
    {
        $this->provider = config('ai.huggingface.provider', 'fal-ai');
        $this->providerModel = config('ai.huggingface.provider_model', 'fal-ai/flux-2/edit');
        $this->token = config('ai.huggingface.token');
        $this->model = config('ai.huggingface.model');
        $this->timeout = (int) config('ai.huggingface.timeout', 120);

        if (empty($this->token)) {
            throw new RuntimeException('HUGGINGFACE_API_TOKEN chưa được cấu hình trong .env');
        }
    }

    public function generateAndSaveFromImage(string $sourceImagePath, string $prompt, string $filename): string
    {
        if (!is_file($sourceImagePath)) {
            throw new RuntimeException("Không tìm thấy ảnh nguồn: {$sourceImagePath}");
        }

        $sourceImage = file_get_contents($sourceImagePath);

        if ($sourceImage === false || $sourceImage === '') {
            throw new RuntimeException('Không đọc được ảnh nguồn để gửi lên Hugging Face');
        }

        Log::info('[HuggingFaceImageService] Bắt đầu gọi Hugging Face API', [
            'model' => $this->model,
            'provider' => $this->provider,
            'provider_model' => $this->providerModel,
            'prompt' => substr($prompt, 0, 120) . '...',
            'source_image' => $sourceImagePath,
        ]);

        $response = Http::withToken($this->token)
            ->accept('image/*')
            ->timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post(self::BASE_URL . $this->provider . '/models/' . $this->providerModel, [
                'inputs' => base64_encode($sourceImage),
                'parameters' => [
                    'prompt' => $prompt,
                    'guidance_scale' => 7.5,
                    'num_inference_steps' => 28,
                    'negative_prompt' => 'blurry, low quality, distorted, watermark, text, extra fingers, extra limbs, deformed',
                ],
                'options' => [
                    'wait_for_model' => true,
                    'use_cache' => false,
                ],
            ]);

        if ($response->failed()) {
            $error = $response->json('error') ?? $response->body();

            Log::error('[HuggingFaceImageService] API gọi thất bại', [
                'status' => $response->status(),
                'error' => $error,
            ]);

            throw new RuntimeException("Hugging Face API lỗi [{$response->status()}]: {$error}");
        }

        $contentType = $response->header('content-type', '');

        if (str_contains($contentType, 'application/json')) {
            $error = $response->json('error') ?? $response->json('message') ?? $response->body();
            throw new RuntimeException("Hugging Face trả về lỗi: {$error}");
        }

        $imageContent = $response->body();

        if (empty($imageContent)) {
            throw new RuntimeException('Hugging Face không trả về ảnh kết quả');
        }

        $relativePath = "generated/{$filename}.png";
        $fullPath = storage_path("app/public/{$relativePath}");

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $imageContent);

        Log::info('[HuggingFaceImageService] Generate thành công', [
            'saved_path' => $relativePath,
        ]);

        return $relativePath;
    }
}