<?php

namespace App\Actions\Api\Quotations;

use App\Models\Quotation;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetStatusQuotationApiAction
{
    public function handle(string $identifyCode): array
    {
        try {
            $quotation = Quotation::with('product')
                ->where('identify_code', $identifyCode)
                ->firstOrFail();

            $statusCode = is_null($quotation->delivery_days)
                ? '01'
                : ($quotation->isCompleted() ? '81' : '20');

            return [
                'response' => [
                    'identify_code' => $quotation->identify_code,
                    'reference_code' => $quotation->reference_code,
                    'success' => 'OK',
                    'status' => $statusCode,
                    'items' => [[
                        'sku' => $quotation->sku,
                        'quantity' => $quotation->quantity,
                        'mpn' => $quotation->product->mpn ?? null,
                        'unit_price' => $quotation->unit_price,
                        'delivery_days' => $quotation->delivery_days,
                    ]]
                ]
            ];
        } catch (Throwable $th) {
            Log::error(__METHOD__, [
                'identifyCode' => $identifyCode,
                'error' => $th->getMessage()
            ]);
            throw $th;
        }
    }
}
