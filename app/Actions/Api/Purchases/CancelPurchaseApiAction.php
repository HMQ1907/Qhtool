<?php

namespace App\Actions\Api\Purchases;

use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelPurchaseApiAction
{
    public function handle(string $identifyCode): array
    {
        try {
            $purchase = Purchase::where('identify_code', $identifyCode)->first();

            if (!$purchase) {
                return [
                    'response' => [
                        'identify_code' => $identifyCode,
                        'reference_code' => '',
                        'status' => NG,
                    ]
                ];
            }

            if (!$purchase->is_canceled) {
                $purchase->update(['is_canceled' => 1]);
            }

            return [
                'response' => [
                    'identify_code' => $identifyCode,
                    'reference_code' => $purchase->reference_code,
                    'status' => OK,
                ]
            ];
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'identifyCode' => $identifyCode,
            ]);

            throw $th;
        }
    }
}
