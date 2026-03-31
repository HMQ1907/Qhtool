<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProductDataOgCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:product-data-og';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product data og';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $listVariantsKeys = DB::connection('eg_product_shadow')
            ->table('selects_shadow')
            ->select('id', 'name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->name];
            });

        $this->info('Processing product data...');

        $processedCount = 0;
        $totalProducts = 0;

        Product::select('sku', 'data', 'hash')->chunk(1000, function ($products) use ($listVariantsKeys, &$processedCount, &$totalProducts) {
            foreach ($products as $product) {
                $totalProducts++;

                $productVariantsOg = DB::connection('pm_30')
                    ->table('product_option')
                    ->join('products', 'products.id', '=', 'product_option.product_id')
                    ->select('product_option.name_local', 'product_option.select_id', 'products.sku', 'product_option.id')
                    ->where('product_country', 'TH')
                    ->where('products.group_code', '!=', '0')
                    ->whereNotNull('name_local')
                    ->where('name_local', '!=', '')
                    ->where('products.sku', $product->sku)
                    ->get()
                    ->map(function ($item) use ($listVariantsKeys) {
                        $key = $listVariantsKeys[$item->select_id] ?? '';
                        return [$key => $item->name_local];
                    })->values()->toArray();

                $productName = DB::connection('pm_30')
                    ->table('product_translations')
                    ->join('products', 'products.id', '=', 'product_translations.product_id')
                    ->select('product_translations.name', 'product_translations.locale')
                    ->where('product_translations.product_country', 'TH')
                    ->where('products.sku', $product->sku)
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item->locale => $item->name];
                    });

                $data = $product->data;

                if ($productName->isEmpty()) {
                    continue;
                }

                if ($productVariantsOg) {
                    $data->variants_og = $productVariantsOg;
                }

                if ($productName) {
                    $data->name_og = $productName['TH'];
                    $data->name = $productName['EN'];
                }

                $product->hash = md5(json_encode($data));
                $product->data = $data;
                $product->updated_at = now();
                $product->save();
                $processedCount++;
            }
        });

        $this->info("Processed {$processedCount} products from {$totalProducts} total SKUs.");
    }
}
