<?php

namespace App\Operations\Api\Purchase;

use App\Operations\Api\BaseOperationApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreatePurchaseApiOperation extends BaseOperationApi
{
    public function handle(array $input = []): array | bool
    {
        try {
            return DB::transaction(function () use ($input) {

                $productData = $this->findProduct($input);

                if (!$productData['supplier'] || !$productData['product']) {
                    return false;
                }

                $sequence = $this->getSequenceAction->handle(PURCHASE);
                $purchase = $this->createPurchaseApiAction->handle($sequence, $input, $productData);

                return [
                    'response' => [
                        'status' => OK,
                        'identify_code' => $purchase->identify_code,
                        'reference_code' => $purchase->reference_code,
                    ]
                ];
            }, NUMBER_TRANSACTION);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
                'error' => $th->getMessage(),
            ]);

            throw $th;
        }
    }
}
