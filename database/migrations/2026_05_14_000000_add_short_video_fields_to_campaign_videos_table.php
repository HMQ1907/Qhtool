<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('campaign_videos', 'external_task_id')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->string('external_task_id')->nullable()->index();
            });
        }

        if (! Schema::hasColumn('campaign_videos', 'external_url_expires_at')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->timestamp('external_url_expires_at')->nullable();
            });
        }

        if (! Schema::hasColumn('campaign_videos', 'duration_seconds')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->unsignedSmallInteger('duration_seconds')->default(30);
            });
        }

        if (! Schema::hasColumn('campaign_videos', 'aspect_ratio')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->string('aspect_ratio', 10)->default('9:16');
            });
        }

        if (! Schema::hasColumn('campaign_videos', 'quality')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->string('quality', 20)->default('720p');
            });
        }

        if (! Schema::hasColumn('campaign_videos', 'caption')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->text('caption')->nullable();
            });
        }

        if (! Schema::hasColumn('campaign_videos', 'hashtags')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->json('hashtags')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'external_url_expires_at',
            'duration_seconds',
            'aspect_ratio',
            'quality',
            'caption',
            'hashtags',
        ] as $column) {
            if (Schema::hasColumn('campaign_videos', $column)) {
                Schema::table('campaign_videos', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('campaign_videos', 'external_task_id')) {
            Schema::table('campaign_videos', function (Blueprint $table) {
                $table->dropIndex('campaign_videos_external_task_id_index');
                $table->dropColumn('external_task_id');
            });
        }
    }
};
