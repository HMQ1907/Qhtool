<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Service chuyên trách giao tiếp với fal.ai API.
 *
 * Tại sao tách Service Layer?
 * - Controller không nên biết fal.ai hoạt động như thế nào
 * - Sau này muốn đổi sang Stable Diffusion / Midjourney → chỉ đổi service này
 * - Dễ mock trong unit test
 * - Dễ mở rộng: FalVideoService sẽ extend cùng pattern này
 */
class FalImageService
{
    /**
     * Endpoint của fal.ai cho image generation (flux v1.1 ultra).
     * Hỗ trợ image-to-image với reference images.
     */
    private const API_URL = 'https://fal.run/fal-ai/flux-pro/v1.1-ultra';

    /**
     * Timeout tối đa cho API call (giây).
     * fal.ai thường mất 15-30s nên set 90s để an toàn.
     */
    private const TIMEOUT_SECONDS = 90;

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('fal.api_key');

        if (empty($this->apiKey)) {
            throw new RuntimeException('FAL_AI_KEY chưa được cấu hình trong .env');
        }
    }

    /**
     * Build prompt chuyên nghiệp từ input đơn giản của user.
     *
     * Đây là bước quan trọng nhất: người dùng chỉ cần nhập tiếng Việt đơn giản,
     * hệ thống tự động chuyển thành prompt kỹ thuật cho AI.
     *
     * @param string $modelName  Tên/mô tả người mẫu (ví dụ: "nữ Hàn")
     * @param string $bgName     Tên background (ví dụ: "quán cafe")
     * @param string|null $userPrompt Mô tả bổ sung từ user (optional)
     */
    public function buildPrompt(string $modelName, string $bgName, ?string $userPrompt = null): string
    {
        $base = "A stylish young {$modelName} model wearing the product clothing, "
              . "in a {$bgName} setting, "
              . "soft natural lighting, professional fashion photography, "
              . "Gen Z aesthetic, high resolution, clean background, "
              . "editorial style, Vietnamese fashion brand look";

        if (!empty($userPrompt)) {
            $base .= ", {$userPrompt}";
        }

        return $base;
    }

    /**
     * Gọi fal.ai API để generate ảnh.
     *
     * Flow:
     * 1. Build payload với prompt + reference images
     * 2. POST lên fal.ai
     * 3. Parse response, trả về URL ảnh kết quả
     *
     * @param string $prompt         Prompt đã được build sẵn
     * @param string $productImageUrl URL công khai của ảnh sản phẩm
     * @param string $modelImageUrl   URL công khai của ảnh người mẫu
     * @return string URL ảnh kết quả từ fal.ai
     * @throws RuntimeException Khi API lỗi hoặc không có ảnh trả về
     */
    public function generateImage(
        string $prompt,
        string $productImageUrl,
        string $modelImageUrl
    ): string {
        Log::info('[FalImageService] Bắt đầu gọi fal.ai API', [
            'prompt'           => substr($prompt, 0, 100) . '...',
            'product_image'    => $productImageUrl,
            'model_image'      => $modelImageUrl,
        ]);

        $payload = $this->buildPayload($prompt, $productImageUrl, $modelImageUrl);

        $response = Http::withHeaders([
            'Authorization' => "Key {$this->apiKey}",
            'Content-Type'  => 'application/json',
        ])
        ->timeout(self::TIMEOUT_SECONDS)
        ->post(self::API_URL, $payload);

        if ($response->failed()) {
            $error = $response->json('detail') ?? $response->body();
            Log::error('[FalImageService] API gọi thất bại', [
                'status' => $response->status(),
                'error'  => $error,
            ]);
            throw new RuntimeException("fal.ai API lỗi [{$response->status()}]: {$error}");
        }

        $imageUrl = $response->json('images.0.url');

        if (empty($imageUrl)) {
            Log::error('[FalImageService] Response không có ảnh', [
                'response' => $response->json(),
            ]);
            throw new RuntimeException('fal.ai không trả về ảnh kết quả');
        }

        Log::info('[FalImageService] Generate thành công', ['image_url' => $imageUrl]);

        return $imageUrl;
    }

    /**
     * Build payload cho fal.ai API.
     *
     * Tách riêng để dễ thay đổi cấu hình model sau này
     * (ví dụ: đổi image_size, num_inference_steps, guidance_scale...).
     */
    private function buildPayload(
        string $prompt,
        string $productImageUrl,
        string $modelImageUrl
    ): array {
        return [
            'prompt'               => $prompt,
            'image_url'            => $productImageUrl, // Ảnh sản phẩm làm reference chính
            'num_images'           => 1,
            'enable_safety_checker'=> true,
            'safety_tolerance'     => '2',
            'output_format'        => 'jpeg',
        ];
    }

    /**
     * Download ảnh từ URL và lưu vào storage.
     *
     * Tại sao download về lưu thay vì dùng URL trực tiếp?
     * - URL từ fal.ai có thể expire sau vài giờ
     * - Đảm bảo ảnh luôn available cho user về sau
     *
     * @param string $imageUrl URL ảnh từ fal.ai
     * @param string $filename Tên file muốn lưu (không có extension)
     * @return string Đường dẫn tương đối trong storage
     */
    public function downloadAndSave(string $imageUrl, string $filename): string
    {
        $imageContent = Http::timeout(30)->get($imageUrl)->body();

        $relativePath = "generated/{$filename}.jpg";
        $fullPath      = storage_path("app/public/{$relativePath}");

        // Tạo thư mục nếu chưa có
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $imageContent);

        return $relativePath;
    }
}
