<?php

namespace App\Http\Controllers\Purchase;

use App\Actions\Purchases\SpreadSheet\SPODataTransformerAction;
use App\Actions\Purchases\SpreadSheet\GetSPOPurchasePreviewAction;
use App\Http\Controllers\Controller;
use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderApproved;
use App\Operations\Purchase\ExportSPOPurchaseSpreadsheetOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PreviewSPOPurchaseController extends Controller
{
    public function __construct(
        protected ExportSPOPurchaseSpreadsheetOperation $exportOperation,
        protected GetSPOPurchasePreviewAction $getSPOPurchasePreviewAction,
        protected SPODataTransformerAction $spoDataTransformerAction
    ) {}

    public function __invoke(Request $request, int $id)
    {
        try {
            $spoId = $request->query('spoId') ?? null;
            $format = $request->query('format', 'pdf');
            $language = $request->query('language', 'en');
            $isSpoApproved = SupplierPurchaseOrder::find($request->input('spoId'))?->status == SupplierPurchaseOrder::STATUS_APPROVED;

            if ($spoId) {
                if ($isSpoApproved) {
                    $data = SupplierPurchaseOrderApproved::where('supplier_purchase_order_id', $spoId)->first();
                } else {
                    $data = SupplierPurchaseOrder::with(['supplier.purchaseTemplate'])->findOrFail($spoId);
                }

                $data = $this->spoDataTransformerAction->handle($data);
            } else {
                $data = $this->getSPOPurchasePreviewAction->handle($id);
            }

            $isPreview = empty($spoId);

            if ($format === 'excel') {
                $filePath = $this->exportOperation->handle($data, 'xlsx', $isPreview, $spoId ? true : false, $isSpoApproved, language: $language);

                return response()->json(['xlsx' => $filePath]);
            }

            $base64Pdf = $this->exportOperation->handle($data, 'pdf', true, language: $language);

            return response()->json(['pdf' => "data:application/pdf;base64,{$base64Pdf}"]);
        } catch (Throwable $th) {
            Log::error(__METHOD__, ['id' => $id, 'input' => $request->input()]);
            throw $th;
        }
    }
}
