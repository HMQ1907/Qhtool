<?php

namespace App\Actions;

use App\Models\Pm\LocalProduct;
use App\Models\Product;

class StoreProductsAction extends BaseAction
{
    public function handle(array $products): Product
    {
        $sku = $products['sku'];
        $data = (object) $products;
        $hash = md5(json_encode($data));

        $product = Product::where('sku', $sku)->first();

        if ($product?->hash == $hash) {
            return $product;
        }

        if (is_numeric($data->price)) {
            $data->price = $this->calculatePrice($sku, $data->price);
        }

        $finalHash = md5(json_encode($data));

        if (!$product) {
            $product = new Product(['sku' => $sku]);
        }

        $product->data = $data;
        $product->hash = $finalHash;
        $product->save();

        return $product;
    }

    private function calculatePrice(string $sku, float $originalPrice): int
    {
        if (str_contains($sku, "th")) {
            $localProduct = LocalProduct::where('sku', $sku)->first();

            if ($localProduct && (int)$localProduct->purchase_price_exVAT !== 0) {
                return $localProduct->purchase_price_exVAT;
            }
        }

        return (int)($originalPrice / 1.1765);
    }
}
