<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DownloadFileController extends Controller
{
    public function __invoke(Request $request)
    {
        $filePath = $request->input('filePath');
        $fullPath = storage_path('app/private/' . $filePath);
        
        if (file_exists($fullPath)) {
            return Response::download($fullPath)->deleteFileAfterSend();
        }
    }
}
