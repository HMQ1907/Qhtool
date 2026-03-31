<?php

namespace App\Actions\Api\Purchases;

use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreatePurchaseApiAction
{
    public function handle(string $sequence, array $input = [], array $productData): Purchase
    {
        try {
            $additionalData = $input['additional_data'] ?? [];

            $purchase = new Purchase([
                'identify_code' => $sequence,
                'request_from' => $input['request_from'],
                'reference_code' => $input['reference_code'],
                'sku' => $input['product']['sku'],
                'quantity' => $input['product']['qty'],
                'supplier_code' =>  $productData['supplier']->code,
                'unit_price' => $productData['product']->price,
                'currency' =>  $productData['supplier']->data->currency,
                'scm_code' => $additionalData['order_foreign_key'] ?? null,
                'purchase_code' => $input['purchase_code'] ?? null,
                'delivery_days' => $input['estimate_delivery_days'] ?? null,
            ]);

            $purchase->save();

            return $purchase;
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'sequence' => $sequence,
                'input' => $input,
                'productData' => $productData,
            ]);

            throw $th;
        }
    }
}
