<?php

namespace App\Exports\Quotations;

use App\Exports\Sheet\Quotations\RecentlyQuotationSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecentlyQuotationExport implements WithMultipleSheets
{
    protected array $groupedQuotations;

    public function __construct(array $groupedQuotations)
    {
        $this->groupedQuotations = $groupedQuotations;
    }

    public function sheets(): array
    {
        $sheets = [];

        if (isset($this->groupedQuotations['data']) && !empty($this->groupedQuotations['data'])) {
            foreach ($this->groupedQuotations['data'] as $quotations) {
                $sheets[] = new RecentlyQuotationSheet((string)($quotations[0]['supplier']['data']->name), $quotations);
            }
        } else {
            $sheets[] = new RecentlyQuotationSheet('Sheet 1', []);
        }

        return $sheets;
    }
}
