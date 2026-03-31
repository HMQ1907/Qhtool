<?php

namespace App\Http\Controllers\Purchase;

use App\Jobs\Purchase\ExportRecentlyPurchaseJob;
use App\Http\Controllers\Controller;
use App\Actions\Purchases\GetRecentlyPurchaseAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ExportRecentlyPurchaseController extends Controller
{
    public function __construct(
        protected GetRecentlyPurchaseAction $action
    ) {}

    public function __invoke(Request $request)
    {
        try {
            $fileName = 'Purchase_Recently_' . Carbon::now()->format('YmdHis') . '.xlsx';
            $filePath = 'downloads/' . $fileName;
            $data = $this->action->handle($request->input());
            $totalRecords = isset($data['data']) ? count($data['data']) : 0;
            ExportRecentlyPurchaseJob::dispatch($data, $filePath);

            return Inertia::render('Export', [
                'filePath' => $filePath,
                'totalRecords' => $totalRecords,
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->input(),
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
