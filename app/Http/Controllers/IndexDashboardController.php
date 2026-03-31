<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class IndexDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            return Inertia::render('Dashboard');
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
