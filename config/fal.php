<?php

return [
    /*
    |--------------------------------------------------------------------------
    | fal.ai API Key
    |--------------------------------------------------------------------------
    | Lấy tại: https://fal.ai/dashboard/keys
    | Đặt vào .env: FAL_AI_KEY=your_key_here
    */
    'api_key' => env('FAL_AI_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Model mặc định để generate ảnh
    |--------------------------------------------------------------------------
    | Có thể đổi sang các model khác của fal.ai mà không cần sửa service
    */
    'image_model' => env('FAL_IMAGE_MODEL', 'fal-ai/flux-pro/v1.1-ultra'),

    /*
    |--------------------------------------------------------------------------
    | Model cho video (sẽ dùng ở tính năng sau)
    |--------------------------------------------------------------------------
    */
    'video_model' => env('FAL_VIDEO_MODEL', 'fal-ai/kling-video/v1.6/pro/image-to-video'),
];
