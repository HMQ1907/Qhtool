<?php

namespace App\Actions\Api\Quotations;

use App\Models\Quotation;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelQuotationApiAction
{
    public function handle(string $identifyCode): array
    {
        try {
            $quotation = Quotation::where('identify_code', $identifyCode)->first();

            if (!$quotation) {
                return [
                    'response' => [
                        'identify_code' => $identifyCode,
                        'reference_code' => null,
                        'status' => NG,
                    ]
                ];
            }

            if (!$quotation->is_canceled) {
                $quotation->update(['is_canceled' => 1]);
            }

            return [
                'response' => [
                    'identify_code' => $identifyCode,
                    'reference_code' => $quotation->reference_code,
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
