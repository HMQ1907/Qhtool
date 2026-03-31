<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\SupplierPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteSPOPurchaseController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                if ($request->has('spoIds')) {
                    $deletedCodes = [];

                    foreach ($request->input('spoIds') as $spoId) {
                        $SPODetail = SupplierPurchaseOrder::where('status', SupplierPurchaseOrder::STATUS_PENDING)->findOrFail($spoId);

                        $SPODetail->purchases->each(function ($purchase) {
                            $purchase->update([
                                'supplier_purchase_order_id' => null,
                            ]);
                        });

                        $deletedCodes[] = $SPODetail->code;
                        $SPODetail->delete();
                    }

                    createManagementToolHistory('SPO Purchases', 'Delete SPOs: ' . implode(', ', $deletedCodes));
                }

                return true;
            }, NUMBER_TRANSACTION);

            if ($result) {
                return redirect()->route('purchase.spo.index')->with('message', [
                    'type' => SUCCESS_MSG,
                    'title' => 'Success',
                    'messages' => 'SPOs deleted successfully.'
                ]);
            }

            return redirect()->route('purchase.spo.index')->with('message', [
                'type' => ERROR_MSG,
                'title' => 'Error',
                'messages' => 'SPO deleted failed.',
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->input(),
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
