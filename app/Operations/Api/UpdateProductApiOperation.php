<?php

namespace App\Operations\Api;

use App\Operations\Api\BaseOperationApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateProductApiOperation extends BaseOperationApi
{
    public function handle(string $sku): array | bool
    {
        try {
            return DB::transaction(function () use ($sku) {
                $productData = $this->getOrderProductsAction->handle($sku);

                $suppliers = $productData['suppliers'] ? array_map(
                    fn($supplier) => $this->storeSuppliersAction->handle($supplier),
                    $productData['suppliers']
                ) : [];

                $product = $productData['product'] ? $this->storeProductsAction->handle($productData['product']) : null;

                return [
                    'response' => [
                        'success' => $productData ? true : false,
                        'sku' => $sku,
                        'product' => $product,
                        'suppliers' => $suppliers,
                    ]
                ];
            }, NUMBER_TRANSACTION);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, ['input' => $sku]);
            throw $th;
        }
    }
}
