<?php

namespace App\Actions\Purchases\SpreadSheet;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AddTableHeadersAction
{
    public function handle(Worksheet $sheet, array $headers, int &$row, array $alphabet = []): void
    {
        foreach ($headers as $index => $header) {
            $sheet->getStyle("{$alphabet[$index]}{$row}")->getFont()->setBold(true);
            $sheet->getStyle("{$alphabet[$index]}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("{$alphabet[$index]}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('fff2cc');
            $sheet->getStyle("{$alphabet[$index]}{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_DOTTED,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
            $sheet->setCellValue("{$alphabet[$index]}{$row}", $header['title']);
        }

        $row++;
    }
}
