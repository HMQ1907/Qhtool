<?php
// Cspell:disable webike
namespace App\Actions;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class HttpApiAction extends BaseAction
{
    public function httpRequest(string $endpoint, array $optionalHeaders = []): PendingRequest
    {
        $webikeApiInfo = Config::get('api.webike');
        $time = time();
        $token = password_hash("{$webikeApiInfo['token']}{$time}", PASSWORD_BCRYPT);

        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'user' => $webikeApiInfo['user'],
            'token' => $token,
            'timestamp' => $time,
        ];

        $headers = array_merge($defaultHeaders, $optionalHeaders);

        return Http::connectTimeout(10)
            ->retry(5, 2000, fn(Exception $exception) => $exception instanceof ConnectionException)
            ->withUrlParameters([
                'endpoint' => $endpoint,
            ])->withHeaders($headers);
    }
}
