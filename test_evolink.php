<?php
$apiKey = "sk-VtPX2mbvzno4jue2GaYsDZZMrW1ELlYqE61dLvAm6QjFOcO8";
$url = "https://api.evolink.ai/v1/audios/generations";

$data = [
    "model" => "qwen-voice-design",
    "voice_prompt" => "A calm middle-aged man",
    "preview_text" => "Hello everyone",
    "preferred_name" => "stoic_" . uniqid()
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);

$response = curl_exec($ch);
echo "Response: " . $response . "\n";
