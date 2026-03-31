<?php

namespace App\Actions\Purchases;

use App\Models\SupplierPurchaseOrder;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreSupplierPurchaseOrderAction
{
    public function handle(array $input): SupplierPurchaseOrder
    {
        try {
            $spo = new SupplierPurchaseOrder();
            $spo->fill($input);
            $spo->save();

            return $spo;
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
