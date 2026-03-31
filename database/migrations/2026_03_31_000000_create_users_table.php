<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            
            // Phân quyền
            $table->enum('role', ['admin', 'user'])->default('user');
            
            // Hỗ trợ dùng thử free
            $table->integer('free_images_left')->default(3); // Tặng 3 ảnh free
            $table->integer('free_videos_left')->default(1); // Tặng 1 video free
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
