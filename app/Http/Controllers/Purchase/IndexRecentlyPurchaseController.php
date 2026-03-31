<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Support\Facades\Cache;
use App\Actions\Purchases\GetRecentlyPurchaseAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\EgZero\Customers;
use App\Models\SupplierPurchaseOrder;
use Inertia\Response;
use Throwable;

class IndexRecentlyPurchaseController extends Controller
{
    protected $cacheTime = 60 * 60 * 24;

    public function __construct(protected GetRecentlyPurchaseAction $getRecentlyPurchaseAction) {}

    public function __invoke(Request $request): Response
    {
        try {
            $customers = Cache::remember('recently_purchase_customers', $this->cacheTime, function () {
                $customerCountries = json_decode(env("CUSTOMER_COUNTRIES"), true);
                return Customers::select('id', 'nickname')
                    ->whereIn('id', $customerCountries)
                    ->get();
            });
            $purchases = $this->getRecentlyPurchaseAction->handle($request->input());
            $spo = SupplierPurchaseOrder::select('id', 'code', 'supplier_code')->where('status', SupplierPurchaseOrder::STATUS_PENDING)->get();

            return Inertia::render('Purchases/Recently', [
                'purchases' => $purchases,
                'customers' => $customers,
                'spo' => $spo,
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
