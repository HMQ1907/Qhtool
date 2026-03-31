<?php

namespace App\Actions\Purchases\SpreadSheet;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AddPurchaseInformationAction
{
    public function handle(Worksheet $sheet, $spo, int &$row, bool $isPreview = false, array $alphabet = [], string $columnLogo = ''): void
    {
        $totalWidth = 0;
        $lastItemIndex = count($spo->supplier->purchaseTemplate->items) - 1;
        $lastColumn = $column = $alphabet[$lastItemIndex];

        for ($i = $lastItemIndex; $i >= 0; $i--) {
            $totalWidth += $spo->supplier->purchaseTemplate->items[$i]['width'];

            if ($totalWidth >= 26) {
                $column = $alphabet[$i] === $columnLogo ? $alphabet[$i + 1] : $alphabet[$i];
                break;
            }
        }

        $sheet->mergeCells("{$column}{$row}:{$lastColumn}{$row}");
        $sheet->setCellValue("{$column}{$row}", $isPreview ? 'Preview Purchase Order' : 'Purchase Order');
        $sheet->getStyle("{$column}{$row}")->getFont()->setBold(true);
        $sheet->getStyle("{$column}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("{$column}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('d9ead3');

        $row++;

        $sheet->mergeCells("{$column}{$row}:{$lastColumn}{$row}");
        $sheet->setCellValue("{$column}{$row}", 'PO. No.: ' . $spo->code);

        $row++;

        $sheet->mergeCells("{$column}{$row}:{$lastColumn}{$row}");
        $sheet->setCellValue("{$column}{$row}", 'Date: ' . Carbon::parse($spo->created_at)->format('Y-m-d'));
    }
}
