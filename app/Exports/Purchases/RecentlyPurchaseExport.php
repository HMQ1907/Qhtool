<?php

namespace App\Exports\Purchases;

use App\Exports\Sheet\Purchases\RecentlyPurchaseSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecentlyPurchaseExport implements WithMultipleSheets
{
    protected array $groupedPurchases;

    public function __construct(array $groupedPurchases)
    {
        $this->groupedPurchases = $groupedPurchases;
    }

    public function sheets(): array
    {
        $sheets = [];

        if (isset($this->groupedPurchases['data']) && !empty($this->groupedPurchases['data'])) {
            foreach ($this->groupedPurchases['data'] as  $purchases) {
                $sheets[] = new RecentlyPurchaseSheet($purchases[0]['supplier']['data']->name, $purchases);
            }
        } else {
            $sheets[] = new RecentlyPurchaseSheet('Sheet 1', []);
        }

        return $sheets;
    }
}
