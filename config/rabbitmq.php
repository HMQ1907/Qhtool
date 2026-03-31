<?php

return [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'user'),
    'password' => env('RABBITMQ_PASSWORD', 'password'),
    'virtual_host' => env('RABBITMQ_VIRTUAL_HOST', 'virtual_host')
];
