<?php

namespace App\Exports\Sheet\Quotations;

use App\Exports\Sheet\BaseSheet;
use App\Actions\Quotations\GetHistoryQuotationAction;
use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class HistoryQuotationSheet extends BaseSheet implements FromQuery, WithMapping, WithChunkReading, WithCustomChunkSize, WithStrictNullComparison
{
    protected array $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function query()
    {
        if ($this->input) {
            return app()->make(GetHistoryQuotationAction::class)->handle($this->input);
        }

        return Quotation::query()->whereRaw('0=1');
    }

    public function title(): string
    {
        return 'History Quotation';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Supplier',
            'SKU',
            'PART NO.',
            'DESCRIPTION',
            'VARIANT',
            'QTY',
            'UNIT PRICE',
            'DELIVERY DAYS',
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
            $row->supplier->data->name ?? '',
            $row->sku ?? '',
            $productData->model_number ?? '',
            $productData->name ?? '',
            $variant ?? '',
            $row->quantity ?? '',
            $row->unit_price ?? '',
            $row->delivery_days ?? ''
        ];
    }
}
