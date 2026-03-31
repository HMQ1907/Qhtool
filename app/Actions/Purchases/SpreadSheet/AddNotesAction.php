<?php

namespace App\Actions\Purchases\SpreadSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AddNotesAction
{
    public function handle(Worksheet $sheet, string|null $note, int &$row, string $columnEnd): void
    {
        if (empty($note)) {
            $row++;
            return;
        }

        $notes = explode("\n", $note);
        $sheet->mergeCells("A{$row}:{$columnEnd}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("A{$row}", 'Note:');
        $row++;

        foreach ($notes as $note) {
            $sheet->mergeCells("A{$row}:{$columnEnd}{$row}");
            $sheet->setCellValue("A{$row}", $note);
            $row++;
        }

        $row++;
    }
}
