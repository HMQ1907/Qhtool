<?php

namespace App\Http\Controllers\StockPurchase;

use App\Actions\StockPurchase\GetStockPurchaseAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class GetListStockPurchaseController extends Controller
{
    public function __construct(
        protected GetStockPurchaseAction $getStockPurchaseAction
    ) {}

    public function __invoke(Request $request): Response
    {
        try {
            return Inertia::render('StockPurchase', [
                'stockPurchases' => $this->getStockPurchaseAction->handle($request->input())
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
