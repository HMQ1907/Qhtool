<?php

namespace App\Http\Controllers\Quotation;

use App\Jobs\Quotation\ExportRecentlyQuotationJob;
use App\Http\Controllers\Controller;
use App\Actions\Quotations\GetRecentlyQuotationAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ExportRecentlyQuotationController extends Controller
{
    public function __construct(
        protected GetRecentlyQuotationAction $action
    ) {}

    public function __invoke(Request $request)
    {
        try {
            $fileName = 'Quotation_Recently_' . Carbon::now()->format('YmdHis') . '.xlsx';
            $filePath = 'downloads/' . $fileName;
            $data = $this->action->handle($request->input());
            $totalRecords = isset($data['data']) ? count($data['data']) : 0;

            ExportRecentlyQuotationJob::dispatch($data, $filePath);

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
