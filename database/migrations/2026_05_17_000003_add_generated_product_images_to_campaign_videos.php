<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_videos', function (Blueprint $table) {
            if (! Schema::hasColumn('campaign_videos', 'generated_product_images')) {
                $table->json('generated_product_images')->nullable()->after('product_images');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaign_videos', function (Blueprint $table) {
            if (Schema::hasColumn('campaign_videos', 'generated_product_images')) {
                $table->dropColumn('generated_product_images');
            }
        });
    }
};
