<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckFileDownloadExistController extends Controller
{
    public function __invoke(Request $request)
    {
        $filePath = $request->input('filePath');
        $total = $request->input('total');
        $fullPath = storage_path('app/private/' . $filePath);

        if (file_exists($fullPath)) {
            return responseWithJson([
                'is_exists' => true,
                'process' => 100,
                'file_path' => $filePath
            ], 'success', 200);
        }

        $tmpFilePath = storage_path('app/private/' . $filePath . '.tmp');

        $process = 0;
        if (file_exists($tmpFilePath)) {
            $process = ((count(file($tmpFilePath)) - 1) / $total) * 100;
        }

        return responseWithJson([
            'is_exists' => false,
            'process' => $process,
            'path' => $filePath
        ], 'success', 200);
    }
}
