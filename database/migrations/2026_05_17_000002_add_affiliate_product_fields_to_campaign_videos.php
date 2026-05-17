<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_videos', function (Blueprint $table) {
            if (! Schema::hasColumn('campaign_videos', 'generation_mode')) {
                $table->string('generation_mode', 30)->default('fast_test')->after('video_type');
            }

            if (! Schema::hasColumn('campaign_videos', 'product_name')) {
                $table->string('product_name')->nullable()->after('generation_mode');
            }

            if (! Schema::hasColumn('campaign_videos', 'product_url')) {
                $table->text('product_url')->nullable()->after('product_name');
            }

            if (! Schema::hasColumn('campaign_videos', 'product_price')) {
                $table->string('product_price', 80)->nullable()->after('product_url');
            }

            if (! Schema::hasColumn('campaign_videos', 'commission_rate')) {
                $table->string('commission_rate', 80)->nullable()->after('product_price');
            }

            if (! Schema::hasColumn('campaign_videos', 'product_description')) {
                $table->text('product_description')->nullable()->after('commission_rate');
            }

            if (! Schema::hasColumn('campaign_videos', 'product_pain_points')) {
                $table->text('product_pain_points')->nullable()->after('product_description');
            }

            if (! Schema::hasColumn('campaign_videos', 'product_reviews')) {
                $table->text('product_reviews')->nullable()->after('product_pain_points');
            }

            if (! Schema::hasColumn('campaign_videos', 'product_images')) {
                $table->json('product_images')->nullable()->after('product_reviews');
            }

            if (! Schema::hasColumn('campaign_videos', 'sales_angle')) {
                $table->string('sales_angle')->nullable()->after('product_images');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaign_videos', function (Blueprint $table) {
            foreach ([
                'sales_angle',
                'product_images',
                'product_reviews',
                'product_pain_points',
                'product_description',
                'commission_rate',
                'product_price',
                'product_url',
                'product_name',
                'generation_mode',
            ] as $column) {
                if (Schema::hasColumn('campaign_videos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
