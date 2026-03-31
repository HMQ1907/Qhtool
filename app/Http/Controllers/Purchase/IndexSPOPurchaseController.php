<?php

namespace App\Http\Controllers\Purchase;

use App\Actions\Purchases\GetSPOPurchaseAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class IndexSPOPurchaseController extends Controller
{
    public function __construct(
        protected GetSPOPurchaseAction $getSPOPurchaseAction
    ) {}

    public function __invoke(Request $request)
    {
        try {
            return Inertia::render('Purchases/SPO', [
                'SPOPurchases' => $this->getSPOPurchaseAction->handle($request->input())
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
