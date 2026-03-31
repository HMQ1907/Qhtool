<?php

namespace App\Http\Controllers\Purchase;

use App\Actions\Purchases\GetSPOPurchaseDetailAction;
use App\Actions\Purchases\SpreadSheet\SPODataTransformerAction;
use App\Exports\Purchases\SPOPurchaseTSVExport;
use App\Http\Controllers\Controller;
use App\Operations\Purchase\ExportSPOPurchaseSpreadsheetOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderApproved;
use App\Services\Export\ExportService;
use Carbon\Carbon;
use Throwable;

class DownloadSPOPurchaseController extends Controller
{
    public function __construct(
        protected ExportSPOPurchaseSpreadsheetOperation $exportOperation,
        protected GetSPOPurchaseDetailAction $getSPOPurchaseDetailAction,
        protected SPODataTransformerAction $spoDataTransformerAction
    ) {}

    public function __invoke(Request $request)
    {
        $isSpoApproved = SupplierPurchaseOrder::find($request->input('id'))?->status == SupplierPurchaseOrder::STATUS_APPROVED;
        $language = $request->input('language', 'en');
        if ($isSpoApproved) {
            $data = SupplierPurchaseOrderApproved::where('supplier_purchase_order_id', $request->input('id'))->first();
        } else {
            $data = SupplierPurchaseOrder::with(['supplier.purchaseTemplate'])->findOrFail($request->input('id'));
        }

        $data = $this->spoDataTransformerAction->handle($data);
        $format = $request->input('format', 'xlsx');

        try {
            if ($format == 'pdf') {
                $filePath = $this->exportOperation->handle($data, 'pdf', isApproved: $isSpoApproved, language: $language);
            } elseif ($format == 'xlsx') {
                $filePath = $this->exportOperation->handle($data, 'xlsx', isApproved: $isSpoApproved, language: $language);
            } elseif ($format == 'tsv') {
                $fileName = 'SPO_Purchase_' . Carbon::now()->format('YmdHis') . '.tsv';
                $filePath = storage_path('app/downloads/' . $fileName);
                $data = $this->getSPOPurchaseDetailAction->handle($request->input('id'))->purchases ?? collect();

                $service = new ExportService(
                    new SPOPurchaseTSVExport($data),
                    $filePath,
                    'tsv'
                );

                $service->handle();
            }
            return Response::download($filePath)->deleteFileAfterSend(true);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->input(),
            ]);

            throw $th;
        }
    }
}
