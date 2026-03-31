<?php

namespace App\Http\Controllers\StockPurchase;

use App\Actions\ValidateTSVFileAction;
use App\Http\Controllers\Controller;
use App\Jobs\StockPurchase\ImportStockPurchaseTSVJob;
use App\Models\StockPurchaseRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportStockPurchaseController extends Controller
{
    protected string $page = 'Stock Purchase';

    public function __construct(
        protected ValidateTSVFileAction $validateTSVFileAction
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $errorMessages = $this->validateTSVFileAction->handle(
                $request->file('file'),
                StockPurchaseRequest::STOCK_PURCHASE_HEADER_EXPORT
            );
            
            if (empty($errorMessages)) {
                $path = $request->file('file')->storeAs('uploads', 'stock_purchase_' . Carbon::now()->format('YmdHis') . '.tsv');
                $serverFileName = basename($path);

                $userActivity = createManagementToolHistory($this->page, "Waiting import Stock Purchase process.<br>
                    <strong>Original file name:</strong> {$request->file('file')->getClientOriginalName()}<br>
                    <strong>On Server file name:</strong> {$serverFileName}
                ");

                ImportStockPurchaseTSVJob::dispatch($userActivity->id, $path, auth()->id ?? 0);

                return redirect()->back()->with('message', [
                    'type' => 'success',
                    'title' => 'Success',
                    'messages' => 'Finish Importing file to queue. Please check for result in History!',
                ]);
            }

            return redirect()->back()->withErrors([
                'type' => ERROR_MSG,
                'title' => 'Error',
                'messages' => implode('<br>', $errorMessages),
            ], 'error');
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
