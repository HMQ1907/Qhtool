<?php

namespace App\Exports\StockPurchase;

use App\Models\StockPurchaseRequest;
use App\Services\Export\Interfaces\FromArray;
use App\Services\Export\Interfaces\WithCustomCsvSettings;
use App\Services\Export\Interfaces\WithHeadings;

class StockPurchaseExport implements FromArray, WithHeadings, WithCustomCsvSettings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return StockPurchaseRequest::STOCK_PURCHASE_HEADER_EXPORT;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "\t",
            'enclosure' => ''
        ];
    }
}
