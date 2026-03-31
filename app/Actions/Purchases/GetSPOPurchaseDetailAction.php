<?php

namespace App\Actions\Purchases;

use Throwable;
use App\Models\SupplierPurchaseOrder;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;

class GetSPOPurchaseDetailAction
{
    public function handle(int $id)
    {
        try {
            return QueryBuilder::for(SupplierPurchaseOrder::class)->with([
                'purchases.orders:id,increment_id,order_scm_code',
                'purchases.product:sku,data',
                'purchases.supplier:code,data',
                'supplier:code,data',
            ])->findOrFail($id);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'id' => $id,
            ]);

            throw $th;
        }
    }
}
