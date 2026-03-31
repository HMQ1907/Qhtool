<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng lưu lịch sử các ảnh được generate bởi AI.
     *
     * Mỗi record = 1 lần user bấm "Tạo ảnh".
     * Status tracking để frontend biết job đang ở giai đoạn nào.
     * Thiết kế mở rộng: user_id sẵn sàng cho subscription sau này.
     */
    public function up(): void
    {
        Schema::create('generated_images', function (Blueprint $table) {
            $table->id();

            // Liên kết với user — dùng để giới hạn usage khi có subscription sau này
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Ảnh sản phẩm do user upload (lưu trong storage/app/public/uploads)
            $table->string('input_image_path');

            // Người mẫu được chọn (đường dẫn tương đối trong /public/images/models)
            $table->string('model_path');

            // Background được chọn (đường dẫn tương đối trong /public/images/background)
            $table->string('background_path');

            // Prompt thô do user nhập (optional). Hệ thống sẽ build prompt thật từ đây
            $table->text('prompt')->nullable();

            // Ảnh kết quả sau khi AI xử lý xong (lưu trong storage hoặc URL từ fal.ai)
            $table->string('output_image_path')->nullable();

            // Trạng thái xử lý — frontend poll field này để update UI
            // pending: vừa tạo, chưa xử lý
            // processing: job đang chạy
            // done: xong, có ảnh kết quả
            // failed: lỗi, xem error_message
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending')->index();

            // Lưu chi tiết lỗi khi status=failed (debug + hiện thông báo cho user)
            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_images');
    }
};
