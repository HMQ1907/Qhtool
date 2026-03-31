<?php

namespace App\Actions\Api\Purchases;

use App\Models\Purchase;

class GetStatusPurchaseApiAction
{
    public function handle(string $identifyCode): array
    {
        $purchase = Purchase::with('product')->where('identify_code', $identifyCode)->first();

        $statusCode = !is_null($purchase->purchase_code) ? '81' : '01';

        return [
            'response' => [
                'identify_code' => $purchase->identify_code,
                'reference_code' => $purchase->reference_code,
                'success' => 'OK',
                'status' => $statusCode,
                'items' => [[
                    'sku' => $purchase->sku,
                    'quantity' => $purchase->quantity,
                    'mpn' => $purchase->product->mpn ?? null,
                    'unit_price' => $purchase->unit_price,
                    'delivery_days' => $purchase->delivery_days,
                    'delivery_on' => $purchase->delivery_on,
                ]]
            ]
        ];
    }
}
