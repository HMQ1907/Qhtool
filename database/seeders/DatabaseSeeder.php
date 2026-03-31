<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Cần thiết để mã hóa password

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản Admin (Vô hạn dùng vì lúc check có `role !== 'admin'`)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Kiểm tra xem đã có email này chưa
            [
                'name' => 'Admin User',
                'password' => Hash::make('123456'), // Mk: 123456
                'role' => 'admin',
                'free_images_left' => 9999, // Thật ra admin thì sẽ ko check biến này, nhưng cứ để
            ]
        );

        // 2. Tạo tài khoản User Thường (Có 3 lượt dùng miễn phí)
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('123456'), // Mk: 123456
                'role' => 'user',
                'free_images_left' => 3, 
                'free_videos_left' => 1,
            ]
        );
    }
}
