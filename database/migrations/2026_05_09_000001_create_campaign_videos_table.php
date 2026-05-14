<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->enum('video_type', ['monetization', 'affiliate'])->default('monetization');

            $table->text('script_text')->nullable();
            $table->string('voice_audio_url')->nullable();
            $table->string('background_video_url')->nullable();
            $table->string('final_video_url')->nullable();
            $table->string('external_task_id')->nullable()->index();
            $table->timestamp('external_url_expires_at')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->default(30);
            $table->string('aspect_ratio', 10)->default('9:16');
            $table->string('quality', 20)->default('720p');
            $table->text('caption')->nullable();
            $table->json('hashtags')->nullable();

            $table->string('status')->default('draft');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_videos');
    }
};
