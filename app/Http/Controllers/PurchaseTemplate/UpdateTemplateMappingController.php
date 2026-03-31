<?php

namespace App\Http\Controllers\PurchaseTemplate;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseTemplate\MappingSupplierTemplateRequest;
use App\Models\Supplier;
use App\Models\SupplierPurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateTemplateMappingController extends Controller
{
    public function __invoke(MappingSupplierTemplateRequest $request, string $code): JsonResponse
    {
        try {
            $message = "Update";

            $result = DB::transaction(function () use ($request, $code, &$message) {
                $supplier = Supplier::findOrFail($code);

                if ($request->purchase_template_id == $supplier->purchase_template_id) {
                    $spoPendingExists = SupplierPurchaseOrder::where('supplier_code', $code)->where('status', SupplierPurchaseOrder::STATUS_PENDING)->exists();

                    if ($spoPendingExists) {
                        return false;
                    }

                    $message = "Remove";
                    $supplier->purchase_template_id = null;
                } else {
                    $supplier->purchase_template_id = $request->purchase_template_id;
                }

                createManagementToolHistory('PurchaseTemplates-Mapping', $message . ' Mapping Purchase Template: Supplier Code ' . $code);

                $supplier->updated_by = Auth::id() ?? 0;
                $supplier->save();

                return true;
            }, NUMBER_TRANSACTION);

            if (!$result) {
                return responseWithJson("Error", 'error', 200);
            }

            return responseWithJson($message, 'success', 200);
        } catch (Throwable $th) {

            Log::error(__METHOD__, [
                'code' => $code,
                'input' => $request->input(),
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
