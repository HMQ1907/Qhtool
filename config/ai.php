<?php

return [
    'provider' => env('AI_PROVIDER', 'fal'),

    'huggingface' => [
        'provider' => env('HUGGINGFACE_INFERENCE_PROVIDER', 'fal-ai'),
        'provider_model' => env('HUGGINGFACE_PROVIDER_MODEL', 'fal-ai/flux-2/edit'),
        'token' => env('HUGGINGFACE_API_TOKEN', ''),
        'model' => env('HUGGINGFACE_IMAGE_MODEL', 'black-forest-labs/FLUX.2-dev'),
        'timeout' => (int) env('HUGGINGFACE_TIMEOUT', 120),
    ],
];