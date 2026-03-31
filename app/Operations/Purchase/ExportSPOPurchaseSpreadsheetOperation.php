<?php

namespace App\Operations\Purchase;

use App\Actions\Purchases\SpreadSheet\AddAuthorizedSignatureAction;
use App\Actions\Purchases\SpreadSheet\AddNotesAction;
use App\Actions\Purchases\SpreadSheet\AddPurchaseInformationAction;
use App\Actions\Purchases\SpreadSheet\AddShipToAddressAction;
use App\Actions\Purchases\SpreadSheet\AddSupplierAddressAction;
use App\Actions\Purchases\SpreadSheet\AddTableDataAction;
use App\Actions\Purchases\SpreadSheet\AddTableHeadersAction;
use App\Actions\Purchases\SpreadSheet\AddWebikeAddressAction;
use App\Actions\Purchases\SpreadSheet\PreparePurchaseDataAction;
use App\Actions\Purchases\SpreadSheet\InitializeSpreadsheetAction;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportSPOPurchaseSpreadsheetOperation
{
    protected $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
    protected $row = 1;

    public function __construct(
        protected InitializeSpreadsheetAction $initializeSpreadsheetAction,
        protected AddPurchaseInformationAction $addPurchaseInformationAction,
        protected AddWebikeAddressAction $addWebikeAddressAction,
        protected AddSupplierAddressAction $addSupplierAddressAction,
        protected AddShipToAddressAction $addShipToAddressAction,
        protected AddTableHeadersAction $addTableHeadersAction,
        protected AddTableDataAction $addTableDataAction,
        protected AddNotesAction $addNotesAction,
        protected AddAuthorizedSignatureAction $addAuthorizedSignatureAction,
        protected PreparePurchaseDataAction $preparePurchaseDataAction
    ) {}

    public function handle($spo, string $format = 'xlsx', bool $isPreview = false, bool $isSpo = false, bool $isApproved = false, string $language = 'en'): string
    {
        $supplierName = $spo->supplier->name ?? $spo->supplier->data->name ?? '';
        $spreadsheet = $this->createSpreadsheet($spo, $isPreview, $isApproved, $language);

        if ($format == 'pdf') {
            return $this->exportToPdf($spreadsheet, $spo, $isPreview, supplierName: $supplierName);
        } elseif ($format == 'xlsx') {
            return $this->exportToXlsx($spreadsheet, $spo, $isPreview, $isSpo, supplierName: $supplierName);
        }

        return "";
    }

    public function createSpreadsheet($spo, bool $isPreview = false, bool $isApproved = false, string $language = 'en'): Spreadsheet
    {
        $spreadsheet = $this->initializeSpreadsheetAction->handle($spo, $this->alphabet);
        $sheet = $spreadsheet->getActiveSheet();

        $totalWidth = collect($spo->supplier->purchaseTemplate->items)->sum('width');
        $totalWidthCenter = 0;
        $columnBeforeCenter = 'A';
        $columnAfterCenter = 'A';
        $columnEnd = $this->alphabet[count($spo->supplier->purchaseTemplate->items) - 1];

        for ($i = 0; $i < count($spo->supplier->purchaseTemplate->items); $i++) {
            $totalWidthCenter += $spo->supplier->purchaseTemplate->items[$i]['width'];

            if ($totalWidthCenter > ceil($totalWidth / 2)) {
                $columnAfterCenter = $this->alphabet[$i];
                if (isset($this->alphabet[$i - 1])) {
                    $columnBeforeCenter = $this->alphabet[$i - 1];
                }

                break;
            }
        }
        $webikeRow = 1;
        $isDisplayWebikeAddress = $spo->supplier->purchaseTemplate->is_display_webike_address ?? false;
        $columnLogo = 'A';
        $this->addWebikeAddressAction->handle($sheet, $spo->supplier->purchaseTemplate, $webikeRow, $columnBeforeCenter, $this->alphabet, $language, $isDisplayWebikeAddress, $columnLogo);
        $this->addPurchaseInformationAction->handle($sheet, $spo, $this->row, $isPreview, $this->alphabet, $columnLogo);
        $this->row = max($this->row, $webikeRow);
        $toAddressRow = $this->row;
        $this->addSupplierAddressAction->handle($sheet, $spo->supplier->code, $this->row, $columnBeforeCenter, $isPreview, $isApproved, $spo->id);
        $this->addShipToAddressAction->handle($sheet, $spo->supplier->purchaseTemplate, $toAddressRow, $columnAfterCenter, $columnEnd, $language);
        $this->addTableHeadersAction->handle($sheet, $spo->supplier->purchaseTemplate->items, $this->row, $this->alphabet);
        $purchaseData = $this->preparePurchaseDataAction->handle($spo->purchases, $spo->supplier->purchaseTemplate->items, $isPreview);
        $this->addTableDataAction->handle(
            $sheet,
            $spo->supplier->purchaseTemplate->items,
            $purchaseData,
            $this->row,
            $spo->supplier->purchaseTemplate->is_display_total_amount,
            $this->alphabet
        );

        $this->addNotesAction->handle($sheet, $spo->supplier->purchaseTemplate->note, $this->row, $columnEnd, $this->alphabet);
        $this->addAuthorizedSignatureAction->handle($sheet, $spo->supplier->purchaseTemplate, $this->row, $this->alphabet);

        return $spreadsheet;
    }

    public function exportToXlsx(Spreadsheet $spreadsheet, $spo, bool $isPreview = false, bool $isSpo = false, string $supplierName = ''): string
    {
        $writer = new Xlsx($spreadsheet);

        if ($isPreview || $isSpo) {
            $writer = new Html($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $html = ob_get_clean();
            $html = str_replace(public_path($spo->supplier->purchaseTemplate->webike_logo_url), $spo->supplier->purchaseTemplate->webike_logo_url, $html);
            $html = str_replace(public_path($spo->supplier->purchaseTemplate->authorized_signature_url), $spo->supplier->purchaseTemplate->authorized_signature_url, $html);

            return $html;
        }

        $directory = storage_path('app/downloads/');
        $filename = "{$spo->code}_{$supplierName}.xlsx";

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . $filename;
        $writer->save($filePath);

        return $filePath;
    }

    public function exportToPdf(Spreadsheet $spreadsheet, $spo, bool $isPreview = false, string $supplierName = ''): string
    {
        $headers = $spo->supplier->purchaseTemplate->items ?? [];
        $htmlWriter = new Html($spreadsheet);
        ob_start();
        $htmlWriter->save('php://output');
        $html = ob_get_clean();

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'tempDir' => storage_path('app/mpdf'),
            'fontDir' => array_merge($fontDirs, [
                resource_path('fonts/Sarabun'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R'  => 'Sarabun-Regular.ttf',
                    'B'  => 'Sarabun-Bold.ttf',
                    'I'  => 'Sarabun-Italic.ttf',
                    'BI' => 'Sarabun-BoldItalic.ttf',
                ],
            ],
            'default_font' => 'sarabun',
        ]);

        $stylesheet = "table { width: 100%; cellpadding: 5px; overflow: wrap }\n";
        $stylesheet .= "td, th { padding: 5px; }\n";

        if (count($headers) == 5) {
            $baseUnit = 7.5;
            foreach ($headers as $i => $h) {
                $colIndex = $i + 1;
                $pxWidth = $h['width'] * $baseUnit;
                $stylesheet .= "th:nth-child($colIndex), td:nth-child($colIndex), colgroup col:nth-child($colIndex) { width: {$pxWidth}px; }\n";
            }
        }

        $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

        $originalLimit = ini_get('pcre.backtrack_limit');
        ini_set('pcre.backtrack_limit', 10000000);
        $mpdf->WriteHTML($html);
        ini_set('pcre.backtrack_limit', $originalLimit);

        if ($isPreview) {
            return base64_encode($mpdf->Output('', Destination::STRING_RETURN));
        }

        $filename = "{$spo->code}_{$supplierName}.pdf";
        $path = storage_path('app/private/downloads/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $mpdf->Output($path, Destination::FILE);

        return $path;
    }
}
