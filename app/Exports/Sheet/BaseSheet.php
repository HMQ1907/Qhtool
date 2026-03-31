<?php

namespace App\Exports\Sheet;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithEvents;

abstract class BaseSheet implements WithTitle, WithHeadings, WithEvents
{
    protected string $supplierName;
    protected array $items;

    public function __construct(string $supplierName = '', array $items = [])
    {
        $this->supplierName = $supplierName;
        $this->items = $items;
    }

    public function title(): string
    {
        return substr($this->supplierName, 0, 31);
    }

    protected function formatVariants(array $variants): string
    {
        return collect($variants)
            ->flatMap(function ($variant) {
                return collect($variant)->map(function ($value, $key) {
                    return "$value";
                });
            })
            ->implode('<br>');
    }

    abstract public function headings(): array;

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $textColumns = ["B", "C", "D", "F"];

                foreach ($textColumns as $column) {
                    for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                        $value = $sheet->getCell("{$column}{$row}")->getValue();
                        if ($value) {
                            $sheet->setCellValueExplicit("{$column}{$row}", (string) $value, DataType::TYPE_STRING);
                        }
                    }
                }
            },
        ];
    }
}
