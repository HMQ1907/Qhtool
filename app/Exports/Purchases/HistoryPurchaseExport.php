<?php

namespace App\Exports\Purchases;

use App\Exports\Sheet\Purchases\HistoryPurchaseSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HistoryPurchaseExport implements WithMultipleSheets
{
    protected array $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function sheets(): array
    {
        return [
            new HistoryPurchaseSheet($this->input)
        ];
    }
}
