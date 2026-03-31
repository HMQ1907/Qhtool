<?php

namespace App\Actions\Purchases\SpreadSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AddAuthorizedSignatureAction
{
    public function handle(Worksheet $sheet, $purchaseTemplate, int &$row, array $alphabet = []): void
    {
        if (empty($purchaseTemplate->authorized_signature_url)) {
            $row++;
            return;
        }

        $totalWidth = 0;
        $lastItemIndex = count($purchaseTemplate->items) - 1;
        $lastColumn = $column = $alphabet[$lastItemIndex];

        for ($i = $lastItemIndex; $i >= 0; $i--) {
            $totalWidth += $purchaseTemplate->items[$i]['width'];

            if ($totalWidth >= 26) {
                $column = $alphabet[$i];
                break;
            }
        }

        $sheet->mergeCells("{$column}{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("{$column}{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("{$column}{$row}", 'Authorized Signature');
        $row++;

        $sheet->getRowDimension($row)->setRowHeight(45);
        $sheet->mergeCells("{$column}{$row}:{$lastColumn}{$row}");
        $resolved = public_path($purchaseTemplate->authorized_signature_url);

        if (file_exists($resolved)) {
            $drawing = new Drawing();
            $drawing->setPath($resolved);
            $drawing->setHeight(44);
            $drawing->setCoordinates("{$column}{$row}");
            $drawing->setWorksheet($sheet);
        }

        $row++;
    }
}
