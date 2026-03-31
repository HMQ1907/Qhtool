<?php

namespace App\Exports\Sheet\Quotations;

use App\Exports\Sheet\BaseSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class RecentlyQuotationSheet extends BaseSheet implements FromArray
{
    public function headings(): array
    {
        return [
            'ID',
            'Part No.',
            'SCM Code',
            'Order Number',
            'DESCRIPTION',
            'Variant',
            'Q.ty',
            'Unit Price',
            'Amount',
        ];
    }

    public function array(): array
    {
        $index = 1;
        return array_map(function ($item) use (&$index) {
            $variants = $item['product']['data']->variants ?? [];
            $variant = $this->formatVariants($variants);

            return [
                $index++,
                $item['product']['data']->model_number ?? '',
                $item['scm_code'] ?? '',
                $item['order_number'] ?? '',
                $item['product']['data']->name ?? '',
                $variant ?? '',
                $item['quantity'] ?? '',
                $item['unit_price'] ?? '',
                $item['quantity'] * $item['unit_price'] ?? ''
            ];
        }, $this->items);
    }
}
