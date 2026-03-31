<?php

namespace App\Operations\Api;

use App\Actions\Api\Purchases\CreatePurchaseApiAction;
use App\Actions\Api\Quotations\CreateQuotationApiAction;
use App\Actions\StoreProductsAction;
use App\Actions\StoreSuppliersAction;
use App\Actions\Sequences\GetSequenceAction;
use App\Actions\Webike\GetOrderProductsAction;
use App\Models\Product;
use App\Models\Supplier;

class BaseOperationApi
{
    public function __construct(
        protected GetOrderProductsAction $getOrderProductsAction,
        protected StoreProductsAction $storeProductsAction,
        protected StoreSuppliersAction $storeSuppliersAction,
        protected CreateQuotationApiAction $createQuotationApiAction,
        protected GetSequenceAction $getSequenceAction,
        protected CreatePurchaseApiAction $createPurchaseApiAction,
    ) {}

    protected function findProduct(array $input): array|bool
    {
        $productData =  $this->getOrderProductsAction->handle($input['product']['sku']);

        return [
            'supplier' =>  $this->storeSupplier($productData['suppliers'], $input['supplier_key']),
            'product' =>  $this->storeProduct($productData['product']),
        ];
    }

    protected function storeSupplier(array $supplierData, string $supplierKey): Supplier
    {
        foreach ($supplierData as $supplier) {
            $this->storeSuppliersAction->handle($supplier);
        }

        return Supplier::where('code', $supplierKey)->first();
    }

    protected function storeProduct(array $productData): Product
    {
        return $this->storeProductsAction->handle($productData);
    }
}
