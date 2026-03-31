<?php

use App\Actions\History\CreateManagementToolHistoryAction;
use App\Actions\History\UpdateManagementToolHistoryAction;
use App\Models\EgZero\Countries;
use App\Models\EgZero\Locations;
use App\Models\ManagementToolHistory;
use Illuminate\Support\Facades\Cache;

if (! function_exists('createManagementToolHistory')) {
    function createManagementToolHistory(string $page, string $operation): ManagementToolHistory
    {
        $createManagementToolHistory = app()->make(CreateManagementToolHistoryAction::class);

        return $createManagementToolHistory->handle($page, $operation);
    }
}

if (! function_exists('updateManagementToolHistory')) {
    function updateManagementToolHistory(int $id, string $operation): ManagementToolHistory
    {
        $updateManagementToolHistory = app()->make(UpdateManagementToolHistoryAction::class);

        return $updateManagementToolHistory->handle($id, $operation);
    }
}

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

if (!function_exists('getCountries')) {
    function getCountries()
    {
        return Cache::remember('countries', 60 * 60 * 24, function () {
            return Countries::select('name')->pluck('name')->toArray();
        });
    }
}

if (!function_exists('getLocations')) {
    function getLocations()
    {
        return Cache::remember('locations', 60 * 60 * 24, function () {
            return Locations::all()->toArray();
        });
    }
}
