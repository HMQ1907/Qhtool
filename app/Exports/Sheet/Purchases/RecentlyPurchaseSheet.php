<?php

namespace App\Exports\Sheet\Purchases;

use App\Exports\Sheet\BaseSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class RecentlyPurchaseSheet extends BaseSheet implements FromArray
{
    public function headings(): array
    {
        return [
            'ID',
            'SKU',
            'Part No.',
            'PO.',
            'SCM Code',
            'Order Number',
            'DESCRIPTION',
            'VARIANT',
            'Q.ty',
            'UNIT PRICE',
            'AMOUNT',
        ];
    }

    public function array(): array
    {
        $index = 1;
        return array_map(function ($item) use (&$index) {
            $productData = $item['product']['data'];
            $variants = $item['product']['data']->variants ?? [];
            $variant = $this->formatVariants($variants);

            return [
                $index++,
                $item['sku'] ?? '',
                $productData->model_number ?? '',
                $item['identify_code'] ?? '',
                $item['orders']['order_scm_code'] ?? '',
                $item['scm_code'] ?? '',
                $productData->name ?? '',
                $variant ?? '',
                $item['quantity'] ?? '',
                $item['unit_price'] ?? '',
                $item['quantity'] * $item['unit_price'] ?? ''
            ];
        }, $this->items);
    }
}
