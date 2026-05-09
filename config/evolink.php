<?php

return [
    'api_key' => env('EVOLINK_API_KEY', ''),
    'base_url' => env('EVOLINK_BASE_URL', 'https://api.evolink.ai/v1'),
    'text_model' => env('EVOLINK_TEXT_MODEL', 'deepseek-v4-pro'),
    'voice_model' => env('EVOLINK_VOICE_MODEL', 'qwen3-tts-vd'),
    'video_model' => env('EVOLINK_VIDEO_MODEL', 'kling-v3-text-to-video'),
];
