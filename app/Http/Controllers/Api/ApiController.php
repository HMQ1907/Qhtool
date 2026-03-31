<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function responseJsonApi(array $data): JsonResponse
    {
        return response()->json($data);
    }
}
