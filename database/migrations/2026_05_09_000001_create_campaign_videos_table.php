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
            
            // Assets generated sequentially
            $table->text('script_text')->nullable()->comment('Kịch bản sinh từ AI');
            $table->string('voice_audio_url')->nullable()->comment('File Audio từ ElevenLabs/Evolink');
            $table->string('background_video_url')->nullable()->comment('File Background .mp4 từ Evolink');
            $table->string('final_video_url')->nullable()->comment('File Output cuối cùng đã ghép bằng FFmpeg');
            
            $table->string('status')->default('draft')->comment('draft, generating_script, generating_voice, generating_media, rendering, completed, failed');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_videos');
    }
};
