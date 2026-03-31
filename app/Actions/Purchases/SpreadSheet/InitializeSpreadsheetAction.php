<?php

namespace App\Actions\Purchases\SpreadSheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class InitializeSpreadsheetAction
{
    public function handle($spo, array $alphabet = []): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $pageMargins = $sheet->getPageMargins();
        
        $pageMargins->setTop(0.3);
        $pageMargins->setLeft(0.2);
        $pageMargins->setRight(0.2);
        $pageMargins->setBottom(0.3);

        $sheet->setTitle($spo->code);
        $sheet->setShowGridlines(false);
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Sarabun');

        foreach ($spo->supplier->purchaseTemplate->items as $index => $header) {
            $sheet->getColumnDimension($alphabet[$index])->setWidth($header['width']);
        }

        return $spreadsheet;
    }
}
