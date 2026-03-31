<?php

namespace App\Exports\Sheet\Purchases;

use App\Exports\Sheet\BaseSheet;
use App\Actions\Purchases\GetHistoryPurchaseAction;
use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class HistoryPurchaseSheet extends BaseSheet implements FromQuery, WithMapping, WithChunkReading, WithCustomChunkSize, WithStrictNullComparison
{
    protected array $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function query()
    {
        if ($this->input) {
            return app()->make(GetHistoryPurchaseAction::class)->handle($this->input);
        }

        return Purchase::query()->whereRaw('0=1');
    }

    public function title(): string
    {
        return 'History Purchase';
    }

    public function headings(): array
    {
        return [
            'ID',
            'PO No.',
            'Purchase Date',
            'Supplier',
            'SKU',
            'Part No.',
            'DESCRIPTION',
            'VARIANT',
            'QTY',
            'SCM Code',
            'Order Number',
            'UNIT PRICE',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function map($row): array
    {
        $productData = $row->product->data;
        $variants = $productData->variants ?? [];
        $variant = $this->formatVariants($variants);

        return [
            $row->identify_code ?? '',
            $row->purchase_code ?? '',
            $row->purchase_date ?? '',
            $row->supplier->data->name ?? '',
            $row->sku ?? '',
            $productData->model_number ?? '',
            $productData->name ?? '',
            $variant ?? '',
            $row->quantity ?? '',
            $row->orders->order_scm_code ?? '',
            $row->scm_code ?? '',
            $row->unit_price ?? ''
        ];
    }
}
