<?php

namespace App\Exports\Purchases;

use App\Models\SupplierPurchaseOrder;
use App\Services\Export\Interfaces\FromArray;
use App\Services\Export\Interfaces\WithCustomCsvSettings;
use App\Services\Export\Interfaces\WithHeadings;
use Illuminate\Database\Eloquent\Collection;

class SPOPurchaseTSVExport implements FromArray, WithHeadings, WithCustomCsvSettings
{
    protected Collection $purchases;

    public function __construct(Collection $purchases)
    {
        $this->purchases = $purchases;
    }

    public function array(): array
    {
        return $this->purchases->map(fn($purchase) => [
            $purchase->identify_code ?? '',
            $purchase->purchase_code ?? '',
            $purchase->purchase_date ?? '',
            $purchase->supplier->data->name ?? '',
            $purchase->sku ?? '',
            $purchase->product->mpn ?? '',
            $purchase->product->name ?? '',
            collect($purchase->product->data->variants)->map(fn($variant) => array_values((array)$variant)[0])->join(', '),
            $purchase->quantity ?? '',
            $purchase->orders->order_scm_code ?? '',
            $purchase->scm_code ?? '',
            $purchase->unit_price ?? '',
        ])->toArray();
    }

    public function headings(): array
    {
        return SupplierPurchaseOrder::SPO_DETAIL_HEADER_EXPORT;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "\t",
            'enclosure' => ''
        ];
    }
}
