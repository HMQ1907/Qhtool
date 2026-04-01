<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('source_image_url');
            $table->string('source_image_title')->nullable();
            $table->string('animation')->default('quay-nhe');
            $table->text('prompt')->nullable();
            $table->string('model')->default('kling-v3-text-to-video');
            $table->unsignedInteger('duration')->default(5);
            $table->string('aspect_ratio', 10)->default('16:9');
            $table->string('quality', 10)->default('720p');
            $table->string('sound', 5)->default('off');
            $table->string('external_task_id')->nullable();
            $table->string('output_video_path')->nullable();
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_videos');
    }
};