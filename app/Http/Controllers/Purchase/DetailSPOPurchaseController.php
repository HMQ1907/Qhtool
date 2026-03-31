<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Actions\Purchases\GetSPOPurchaseDetailAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class DetailSPOPurchaseController extends Controller
{
    public function __construct(
        protected GetSPOPurchaseDetailAction $getSPOPurchaseDetailAction
    ) {}

    public function __invoke(Request $request, int $id)
    {
        try {
            $SPODetail = $this->getSPOPurchaseDetailAction->handle($id);

            return Inertia::render('Purchases/SPO/Detail', [
                'SPODetail' => $SPODetail
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
