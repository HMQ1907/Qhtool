<?php

namespace App\Actions\Api\Quotations;

use App\Models\Quotation;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateQuotationApiAction

{
    public function handle(string $sequence, array $input = [], array $productData): Quotation
    {
        try {
            $quotation = Quotation::where('reference_code',  $input['reference_code'] ?? '')->first();

            if ($quotation) return $quotation;

            $quotation = new Quotation([
                'identify_code' => $sequence,
                'request_from' => $input['request_from'],
                'reference_code' => $input['reference_code'],
                'sku' => $input['product']['sku'],
                'quantity' => $input['product']['qty'],
                'supplier_code' =>  $productData['supplier']->code,
                'unit_price' => $productData['product']->price,
                'currency' =>  $productData['supplier']->data->currency,
                'scm_code' => $input['additional_data']['order_foreign_key'] ?? null,
            ]);

            $quotation->save();

            return $quotation;
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
