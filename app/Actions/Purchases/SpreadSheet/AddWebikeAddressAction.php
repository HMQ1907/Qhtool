<?php
// cspell:ignore webike
namespace App\Actions\Purchases\SpreadSheet;

use App\Models\PurchaseTemplate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use stdClass;

class AddWebikeAddressAction
{
    public function handle(
        Worksheet $sheet,
        PurchaseTemplate|stdClass $purchaseTemplate,
        int &$row,
        string $columnBeforeCenter,
        array $alphabet = [],
        string $language = 'en',
        bool $isDisplayWebikeAddress = false,
        string &$columnLogo = 'A'
    ): void {
        
        $webikeAddressMapping = [
            "webike_logo_url",
            "webike_name",
            "webike_street_address",
            "webike_district_sub_district",
        ];

        foreach ($webikeAddressMapping as $key) {
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            if ($key === 'webike_logo_url') {
                $sheet->getRowDimension($row)->setRowHeight(68);

                $totalWidth = 0;
                
                for ($i = 1; $i < count($purchaseTemplate->items); $i++) {
                    $totalWidth += $purchaseTemplate->items[$i]['width'];

                    if ($totalWidth > ceil(285 / 7.5)) {
                        $columnLogo = $alphabet[$i];
                        break;
                    }
                }

                $sheet->mergeCells("A{$row}:{$columnLogo}{$row}");

                if ($purchaseTemplate->webike_logo_url) {
                    $resolvedPath = public_path($purchaseTemplate->webike_logo_url);
                    if (file_exists($resolvedPath)) {
                        $drawing = new Drawing();
                        $drawing->setPath($resolvedPath);
                        $drawing->setHeight(68);
                        $drawing->setCoordinates("A{$row}");
                        $drawing->setWorksheet($sheet);
                    }
                }
            } else if ($isDisplayWebikeAddress && $key !== 'webike_logo_url') {
                if ($key == 'webike_name') {
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                if ($purchaseTemplate->{$key} && $key == 'webike_district_sub_district') {
                    $text = explode('/', $purchaseTemplate->{$key});
                    $purchaseTemplate->{$key} = $language == 'en' ? $text[1] : $text[0];
                }
                $sheet->mergeCells("A{$row}:{$columnBeforeCenter}{$row}");
                $value = $purchaseTemplate->{$key};

                $sheet->setCellValue("A{$row}", $value);
            }

            $row++;
        }

        $row++;
    }
}
