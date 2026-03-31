<?php

namespace App\Exports\Quotations;

use App\Exports\Sheet\Quotations\HistoryQuotationSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HistoryQuotationExport implements WithMultipleSheets
{
    protected array $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function sheets(): array
    {
        return [
            new HistoryQuotationSheet($this->input)
        ];
    }
}
