<?php

namespace App\Actions\Purchases\SpreadSheet;

use App\Models\PurchaseTemplate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use stdClass;

class AddShipToAddressAction
{
    protected $shipToMapping = [
        "ship_to_name",
        "ship_to_street_address",
        "ship_to_district_sub_district",
    ];

    public function handle(Worksheet $sheet, PurchaseTemplate|stdClass $purchaseTemplate, int &$row, string $columnAfterCenter, string $columnEnd, string $language = 'en'): void
    {
        $shipToStartColumn = $columnAfterCenter;

        if ($columnAfterCenter === 'A') {
            $shipToStartColumn = 'B';
        }

        $sheet->getStyle("{$shipToStartColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("{$shipToStartColumn}{$row}")->getFont()->setBold(true);
        $sheet->mergeCells("{$shipToStartColumn}{$row}:{$columnEnd}{$row}");
        $sheet->setCellValue("{$shipToStartColumn}{$row}", 'Ship to: ');

        $row++;

        foreach ($this->shipToMapping as $key) {
            $sheet->getStyle("{$shipToStartColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            if ($key === 'ship_to_name') {
                $sheet->getStyle("{$shipToStartColumn}{$row}")->getFont()->setBold(true);
            }

            if (
                $purchaseTemplate->ship_to_country == 'Thailand'
                && $purchaseTemplate->{$key}
                && $key == 'ship_to_district_sub_district'
            ) {
                $text = explode('/', $purchaseTemplate->{$key});
                $purchaseTemplate->{$key} = $language == 'en' ? $text[1] : $text[0];
            }

            $sheet->mergeCells("{$shipToStartColumn}{$row}:{$columnEnd}{$row}");
            $sheet->setCellValue("{$shipToStartColumn}{$row}", $purchaseTemplate->{$key});
            $row++;
        }

        $row++;
    }
}
