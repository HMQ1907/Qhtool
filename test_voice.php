<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = config("evolink.api_key");
$response = Illuminate\Support\Facades\Http::withToken($apiKey)
    ->post("https://api.evolink.ai/v1/audios/generations", [
        "model" => "qwen3-tts-vd",
        "prompt" => "Hello, this is a test.",
        "voice" => "male_deep"
    ]);
echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";

