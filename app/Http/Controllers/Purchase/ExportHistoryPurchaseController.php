<?php

namespace App\Http\Controllers\Purchase;

use App\Actions\Purchases\GetHistoryPurchaseAction;
use App\Http\Controllers\Controller;
use App\Jobs\Purchase\ExportHistoryPurchaseJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ExportHistoryPurchaseController extends Controller
{
    public function __construct(
        protected GetHistoryPurchaseAction $action
    ) {}

    public function __invoke(Request $request)
    {
        try {
            $fileName = 'Purchase_History_' . Carbon::now()->format('YmdHis') . '.xlsx';
            $filePath = 'downloads/' . $fileName;
            $totalRecords = $this->action->handle($request->input())->count();

            ExportHistoryPurchaseJob::dispatch($request->input(), $filePath);
            
            return Inertia::render('Export', [
                'filePath' => $filePath,
                'totalRecords' => $totalRecords,
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->input(),
            ]);

            throw $th;
        }
    }
}
