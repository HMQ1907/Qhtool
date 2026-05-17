<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE campaign_videos MODIFY voice_audio_url TEXT NULL');
        DB::statement('ALTER TABLE campaign_videos MODIFY background_video_url TEXT NULL');
        DB::statement('ALTER TABLE campaign_videos MODIFY final_video_url TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE campaign_videos MODIFY voice_audio_url VARCHAR(255) NULL');
        DB::statement('ALTER TABLE campaign_videos MODIFY background_video_url VARCHAR(255) NULL');
        DB::statement('ALTER TABLE campaign_videos MODIFY final_video_url VARCHAR(255) NULL');
    }
};
