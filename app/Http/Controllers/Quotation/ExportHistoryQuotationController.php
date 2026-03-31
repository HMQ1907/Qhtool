<?php

namespace App\Http\Controllers\Quotation;

use App\Actions\Quotations\GetHistoryQuotationAction;
use App\Http\Controllers\Controller;
use App\Jobs\Quotation\ExportHistoryQuotationJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ExportHistoryQuotationController extends Controller
{
    public function __construct(
        protected GetHistoryQuotationAction $action
    ) {}

    public function __invoke(Request $request)
    {
        try {
            $fileName = 'Quotation_History_' . Carbon::now()->format('YmdHis') . '.xlsx';
            $filePath = 'downloads/' . $fileName;
            $totalRecords = $this->action->handle($request->input())->count();

            ExportHistoryQuotationJob::dispatch($request->input(), $filePath);

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
