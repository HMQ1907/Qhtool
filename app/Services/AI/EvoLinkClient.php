<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class EvoLinkClient
{
    private const BASE_URL = 'https://api.evolink.ai';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('ai.evolink.api_key', '');

        if (empty($this->apiKey)) {
            throw new RuntimeException('EVOLINK_API_KEY chưa được cấu hình trong .env');
        }
    }

    public function createImageTask(array $payload): array
    {
        return $this->request('POST', '/v1/images/generations', $payload);
    }

    public function createVideoTask(array $payload): array
    {
        return $this->request('POST', '/v1/videos/generations', $payload);
    }

    public function getTask(string $taskId): array
    {
        return $this->request('GET', "/v1/tasks/{$taskId}");
    }

    public function waitForTask(string $taskId, int $timeoutSeconds, int $pollIntervalSeconds = 4): array
    {
        $startedAt = time();

        do {
            $task = $this->getTask($taskId);
            $status = $task['status'] ?? null;

            if ($status === 'completed') {
                return $task;
            }

            if ($status === 'failed') {
                $message = $task['error']['message'] ?? 'EvoLink task failed';

                throw new RuntimeException($message);
            }

            sleep($pollIntervalSeconds);
        } while ((time() - $startedAt) < $timeoutSeconds);

        throw new RuntimeException('EvoLink task timeout');
    }

    public function downloadResult(string $url, string $filename, string $directory, string $extension): string
    {
        $response = Http::timeout(60)->get($url);

        if ($response->failed()) {
            throw new RuntimeException('Không thể tải file kết quả từ EvoLink');
        }

        $relativePath = trim($directory, '/') . '/' . $filename . '.' . ltrim($extension, '.');

        Storage::disk('public')->put($relativePath, $response->body());

        return $relativePath;
    }

    private function request(string $method, string $uri, array $payload = []): array
    {
        $response = Http::baseUrl(self::BASE_URL)
            ->acceptJson()
            ->asJson()
            ->withToken($this->apiKey)
            ->timeout(180)
            ->send($method, $uri, empty($payload) ? [] : ['json' => $payload]);

        if ($response->failed()) {
            $message = $response->json('error.message')
                ?? $response->json('message')
                ?? $response->body();

            throw new RuntimeException("EvoLink API lỗi [{$response->status()}]: {$message}");
        }

        return $response->json() ?? [];
    }
}