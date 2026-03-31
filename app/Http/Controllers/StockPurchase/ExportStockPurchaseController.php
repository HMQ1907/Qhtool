<?php

namespace App\Http\Controllers\StockPurchase;

use App\Exports\StockPurchase\StockPurchaseExport;
use App\Http\Controllers\Controller;
use App\Services\Export\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ExportStockPurchaseController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        try {
            $filePath = storage_path('app/downloads/stock_purchase' . '.tsv');

            $exportService = new ExportService(
                new StockPurchaseExport(),
                $filePath,
                'tsv'
            );

            $exportService->handle();

            return Response::download($filePath)->deleteFileAfterSend(true);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
