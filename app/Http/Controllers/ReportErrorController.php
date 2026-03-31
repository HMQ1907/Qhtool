<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportErrorController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $error = $request->input('error', 'No error message');
        $stack = $request->input('stack', 'No stack trace');
        $source = $request->input('source', 'Unknown source');
        $line = $request->input('line', null);
        $column = $request->input('column', null);
        $component = $request->input('component', 'Unknown component');
        $info = $request->input('info', null);
        $timestamp = $request->input('timestamp', now());

        Log::error('📌 Frontend Error Reported:', [
            'message' => $error,
            'stack' => $stack,
            'source' => $source,
            'line' => $line,
            'column' => $column,
            'component' => $component,
            'info' => $info,
            'timestamp' => $timestamp,
        ]);

        return responseWithJson(['status' => 'Error logged.'], 'success', 200);
    }
}
