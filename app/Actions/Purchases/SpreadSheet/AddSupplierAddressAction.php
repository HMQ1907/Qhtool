<?php

namespace App\Actions\Purchases\SpreadSheet;

use App\Models\Pm\LocalSupplier;
use App\Models\SupplierPurchaseOrderApproved;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AddSupplierAddressAction
{
    protected $toMapping = [
        "supplier_name",
        "supplier_address",
        "supplier_address_district",
    ];

    public function handle(Worksheet $sheet, string $supplierCode, int &$row, string $columnBeforeCenter, bool $isPreview = false, bool $isApproved = false, ?int $spoId = null): void
    {
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->mergeCells("A{$row}:{$columnBeforeCenter}{$row}");
        $sheet->setCellValue("A{$row}", 'To: ');
        $row++;

        if ($isPreview) {
            $localSupplier = [
                'supplier_name' => 'Preview Supplier Name',
                'supplier_address' => 'Preview Supplier Address',
                'supplier_address_district' => 'Preview Supplier District',
            ];
        } else if ($isApproved) {
            $localSupplier = SupplierPurchaseOrderApproved::select('supplier_name', 'supplier_address', 'supplier_address_district')->where('supplier_purchase_order_id', $spoId)->first()->toArray();
        } else {
            $localSupplier = LocalSupplier::where('supplier_code', $supplierCode)
                ->select('name as supplier_name', 'supplier_address', 'supplier_address_district')
                ->first()->toArray();
        }

        foreach ($this->toMapping as $key) {
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            if ($key === 'supplier_name') {
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            }

            $sheet->mergeCells("A{$row}:{$columnBeforeCenter}{$row}");
            $value = $localSupplier[$key];

            $sheet->setCellValue("A{$row}", $value);
            $row++;
        }

        $row++;
    }
}
