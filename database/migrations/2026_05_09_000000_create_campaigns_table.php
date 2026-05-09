<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the campaign');
            $table->string('niche')->default('Psychology & Stoicism');
            $table->text('base_prompt')->nullable();
            $table->string('affiliate_link')->nullable();
            $table->integer('total_videos')->default(1);
            $table->integer('affiliate_ratio')->default(30)->comment('Percentage of affiliate videos');
            $table->string('status')->default('draft')->comment('draft, generating, completed, failed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
