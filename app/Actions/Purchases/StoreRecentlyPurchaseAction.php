<?php

namespace App\Actions\Purchases;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreRecentlyPurchaseAction
{
    public function handle(array $input)
    {
        try {
            if (empty($input)) {
                return false;
            }

            $listPurchase = $input['purchases'][0]['rows'];

            DB::transaction(function () use ($listPurchase, $input) {
                foreach ($listPurchase as $purchaseData) {
                    $purchase = Purchase::where('identify_code', $purchaseData['identify_code'])->first();

                    if ($purchase) {

                        if (is_numeric($purchaseData['unit_price'])) {
                            $purchase->unit_price = $purchaseData['unit_price'];
                        }

                        if (is_numeric($purchaseData['delivery_days'])) {
                            $purchase->delivery_days = $purchaseData['delivery_days'];
                        }

                        $purchase->supplier_purchase_order_id = $input['purchases'][0]['spo_id'];
                        $purchase->save();
                    }
                }

                $listIdentifyCode = array_column($listPurchase, 'identify_code');
                createManagementToolHistory('Purchases', 'Add Purchases: ' . implode(',', $listIdentifyCode));
            }, NUMBER_TRANSACTION);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
