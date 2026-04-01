<?php

namespace App\Jobs;

use App\Models\GeneratedImage;
use App\Services\AI\ImageGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Job xử lý việc generate ảnh AI ở background.
 *
 * Tại sao phải dùng Queue?
 * - EvoLink generate mất 20-30 giây → không thể block HTTP request
 * - Nếu xử lý sync: user phải chờ 30s, timeout, UX tệ
 * - Queue Redis: dispatch ngay lập tức, worker xử lý nền, user poll kết quả
 * - Scale được: khi cần, chạy nhiều worker song song
 */
class GenerateImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Số lần retry nếu job thất bại.
      * Giới hạn 2 lần để tránh spam API và tốn credit EvoLink.
     */
    public int $tries = 2;

    /**
     * Timeout tối đa cho job (giây).
      * EvoLink có thể mất tới 60s cho ảnh phức tạp.
     */
    public int $timeout = 120;

    public function __construct(
        private readonly GeneratedImage $generatedImage
    ) {}

    /**
     * Pipeline xử lý generate ảnh, chia làm 5 bước rõ ràng.
     */
    public function handle(ImageGenerationService $aiService): void
    {
        Log::info("[GenerateImageJob] Bắt đầu xử lý ID: {$this->generatedImage->id}");

        try {
            // ── Bước 1: Cập nhật status → processing ──────────────────────────
            // Frontend đang poll field này. Cập nhật ngay để user thấy "đang xử lý"
            $this->generatedImage->markAsProcessing();

            // ── Bước 2: Build prompt từ input của user ──────────────────────────
            // Chuyển thông tin người dùng chọn thành prompt chuyên nghiệp cho AI
            $modelName  = $this->extractNameFromPath($this->generatedImage->model_path);
            $bgName     = $this->extractNameFromPath($this->generatedImage->background_path);
            $userPrompt = $this->generatedImage->prompt;

            $prompt = $aiService->buildPrompt($modelName, $bgName, $userPrompt);

            Log::info("[GenerateImageJob] Prompt đã build", ['prompt' => $prompt]);

            // ── Bước 3: Gọi EvoLink API ────────────────────────────────────────
            // Encode ảnh thành Base64 để EvoLink không phụ thuộc vào URL công khai.
            $sourceImagePath  = Storage::disk('public')->path($this->generatedImage->input_image_path);
            $modelImagePath   = public_path(ltrim($this->generatedImage->model_path, '/'));
            $backgroundImagePath = public_path(ltrim($this->generatedImage->background_path, '/'));

            // ── Bước 4: Download và lưu ảnh kết quả ──────────────────────────────
            $filename   = 'img_' . $this->generatedImage->id . '_' . Str::random(8);
            $savedPath  = $aiService->generateImage(
                $prompt,
                $sourceImagePath,
                $modelImagePath,
                $backgroundImagePath,
                $filename
            );

            // ── Bước 5: Cập nhật DB status → done ────────────────────────────────
            // Frontend đang poll → sẽ thấy done và hiển thị ảnh ngay
            $this->generatedImage->markAsDone($savedPath);

            Log::info("[GenerateImageJob] Hoàn thành ID: {$this->generatedImage->id}", [
                'output_path' => $savedPath,
            ]);

        } catch (Throwable $e) {
            // ── Error Handling ────────────────────────────────────────────────────
            // Bắt mọi loại lỗi (API lỗi, mạng, timeout, v.v.)
            // Lưu lại error_message để debug và hiện thông báo cho user
            Log::error("[GenerateImageJob] Thất bại ID: {$this->generatedImage->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->generatedImage->markAsFailed(
                "Lỗi xử lý: {$e->getMessage()}"
            );
        }
    }

    /**
     * Trích xuất tên mô tả từ đường dẫn file để build prompt.
     *
     * Ví dụ: "/images/models/nu-han-02.jpg" → "nu han 02"
     * Hệ thống sẽ build prompt từ tên này
     */
    private function extractNameFromPath(string $path): string
    {
        $filename  = pathinfo($path, PATHINFO_FILENAME);
        $cleanName = str_replace(['-', '_'], ' ', $filename);

        return $cleanName;
    }

    /**
     * Xử lý khi job thất bại sau tất cả các lần retry.
     */
    public function failed(Throwable $exception): void
    {
        Log::critical("[GenerateImageJob] Job thất bại hoàn toàn ID: {$this->generatedImage->id}", [
            'error' => $exception->getMessage(),
        ]);

        $this->generatedImage->markAsFailed(
            'Hệ thống không thể xử lý yêu cầu này. Vui lòng thử lại sau.'
        );

        // Hoàn lại lượt tạo ảnh do bị lỗi hệ thống
        $user = \App\Models\User::find($this->generatedImage->user_id);
        if ($user && $user->role !== 'admin') {
            $user->increment('free_images_left');
        }
    }
}
