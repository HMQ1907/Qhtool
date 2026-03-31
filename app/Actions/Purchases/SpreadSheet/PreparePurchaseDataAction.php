<?php

namespace App\Actions\Purchases\SpreadSheet;

use Illuminate\Support\Str;

class PreparePurchaseDataAction
{
    public function handle($purchases, array $headers, bool $isPreview = false): array
    {
        $data = [];
        $mappings = array_column($headers, 'mapping');

        if ($isPreview) {
            foreach (range(1, 10) as $i) {
                $row = [];
                foreach ($mappings as $mapping) {
                    if ($mapping == 'image') {
                        $row[$mapping] = "https://thimg.webike.net/products/2021/07/07/86831-K2F-T10ZB_th.jpg";
                    } elseif ($mapping == 'quantity') {
                        $row[$mapping] = 1 * $i;
                    } elseif ($mapping == 'unit_price') {
                        $row[$mapping] = 1000 * $i;
                    } elseif ($mapping == 'amount') {
                        $row[$mapping] = (1000 * $i) * (1 * $i);
                    } else {
                        $row[$mapping] = $mapping == 'no'
                            ? $i
                            : "Preview " . Str::of($mapping)
                            ->replace('_', ' ')
                            ->title() . " {$i}";
                    }
                }
                $data[] = $row;
            }
            return $data;
        }

        $index = 1;

        foreach ($purchases as $purchase) {
            $row = [];
            foreach ($mappings as $mapping) {
                $row[$mapping] = $mapping == 'no' ? $index++ : data_get($purchase, $mapping, '');
                if ($mapping == 'variant_eng') {
                    $row[$mapping] = $this->getVariant($purchase->product->data->variants);
                }

                if ($mapping == 'description_local') {
                    $row[$mapping] = $purchase->product->data->name_og ?? $purchase->product->data->name;
                }

                if ($mapping == 'variant_local') {
                    $row[$mapping] = isset($purchase->product->data->variants_og) && !empty($purchase->product->data->variants_og) ? $this->getVariant($purchase->product->data->variants_og) : $this->getVariant($purchase->product->data->variants);
                }
            }
            $data[] = $row;
        }

        return $data;
    }

    private function getVariant(array $variants): string
    {
        return collect($variants)->map(fn($variant) => array_values((array)$variant)[0])->join(', ');
    }
}
