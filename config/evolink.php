<?php

return [
    'api_key' => env('EVOLINK_API_KEY', ''),
    'base_url' => env('EVOLINK_BASE_URL', 'https://api.evolink.ai/v1'),
    'text_model' => env('EVOLINK_TEXT_MODEL', 'deepseek-v4-pro'),
    'voice_model' => env('EVOLINK_VOICE_MODEL', 'qwen3-tts-vd'),
    'video_model' => env('EVOLINK_VIDEO_MODEL', 'kling-v3-text-to-video'),
    'video_aspect_ratio' => env('EVOLINK_VIDEO_ASPECT_RATIO', '9:16'),
    'video_quality' => env('EVOLINK_VIDEO_QUALITY', '720p'),
    'video_sound' => env('EVOLINK_VIDEO_SOUND', 'off'),
    'monetization_duration' => (int) env('EVOLINK_MONETIZATION_VIDEO_DURATION', 30),
    'affiliate_duration' => (int) env('EVOLINK_AFFILIATE_VIDEO_DURATION', 45),
    'poll_interval' => (int) env('EVOLINK_POLL_INTERVAL', 5),
    'video_timeout' => (int) env('EVOLINK_VIDEO_TIMEOUT', 900),
];
