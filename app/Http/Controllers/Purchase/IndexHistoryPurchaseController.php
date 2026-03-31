<?php

namespace App\Http\Controllers\Purchase;

use App\Actions\Purchases\GetHistoryPurchaseAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class IndexHistoryPurchaseController extends Controller
{
    public function __construct(protected GetHistoryPurchaseAction $getHistoryPurchaseAction) {}

    public function __invoke(Request $request): Response
    {
        try {
            $filter = $request->input();
            $purchases = $filter ? $this->getHistoryPurchaseAction->handle($filter)->paginate($request->input('per_page', DEFAULT_PER_PAGE))->onEachSide(EACH_SIZE_PAGINATION) : [];

            return Inertia::render('Purchases/History', [
                'purchases' => $purchases
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
