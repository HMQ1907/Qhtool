<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_cleanups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('input_video_path');
            $table->string('input_original_name')->nullable();
            $table->decimal('left_pct', 5, 2)->default(34);
            $table->decimal('top_pct', 5, 2)->default(86);
            $table->decimal('width_pct', 5, 2)->default(31);
            $table->decimal('height_pct', 5, 2)->default(9);
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending')->index();
            $table->string('output_video_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_cleanups');
    }
};
