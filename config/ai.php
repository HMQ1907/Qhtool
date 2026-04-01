<?php

return [
    'evolink' => [
        'api_key' => env('EVOLINK_API_KEY', ''),
        'image_model' => env('EVOLINK_IMAGE_MODEL', 'nano-banana-pro'),
        'video_model' => env('EVOLINK_VIDEO_MODEL', 'kling-v3-text-to-video'),
        'image_quality' => env('EVOLINK_IMAGE_QUALITY', '2K'),
        'video_duration' => (int) env('EVOLINK_VIDEO_DURATION', 5),
        'video_aspect_ratio' => env('EVOLINK_VIDEO_ASPECT_RATIO', '16:9'),
        'video_quality' => env('EVOLINK_VIDEO_QUALITY', '720p'),
        'video_sound' => env('EVOLINK_VIDEO_SOUND', 'off'),
        'image_timeout' => (int) env('EVOLINK_IMAGE_TIMEOUT', 360),
        'video_timeout' => (int) env('EVOLINK_VIDEO_TIMEOUT', 420),
        'poll_interval' => (int) env('EVOLINK_POLL_INTERVAL', 4),
    ],
];