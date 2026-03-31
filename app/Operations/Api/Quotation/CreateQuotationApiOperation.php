<?php

namespace App\Operations\Api\Quotation;

use App\Operations\Api\BaseOperationApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateQuotationApiOperation extends BaseOperationApi
{
    public function handle(array $input = []): array | bool
    {
        try {
            return DB::transaction(function () use ($input) {

                $productData = $this->findProduct($input);

                if (!$productData['supplier'] || !$productData['product']) {
                    return false;
                }

                $sequence = $this->getSequenceAction->handle(QUOTATION);
                $quotation = $this->createQuotationApiAction->handle($sequence, $input, $productData);

                return [
                    'response' => [
                        'status' => OK,
                        'identify_code' => $quotation->identify_code,
                        'reference_code' => $quotation->reference_code,
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
