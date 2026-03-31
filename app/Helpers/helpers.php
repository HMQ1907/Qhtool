<?php

if (! function_exists('responseWithJson')) {
    function responseWithJson(mixed $data, string $status, int $httpCode, array $headers = [])
    {
        $wrappedData = [
            'status' => $status,
            'data' => $data,
        ];

        return response()->json($wrappedData, $httpCode, $headers);
    }
}

if (! function_exists('responseErrorWithJson')) {
    function responseErrorWithJson(mixed $data, string $status, int $httpCode)
    {
        $wrappedData = [
            'status' => $status,
            'error' => $data,
        ];

        return response()->json($wrappedData, $httpCode);
    }
}

if (!function_exists('isEmpty')) {
    function isEmpty($value)
    {
        return $value === 0 || $value === '0' ? false : empty($value);
    }
}