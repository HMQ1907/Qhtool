<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteSPOPurchaseItemController extends Controller
{
    public function __invoke(Request $request, int $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);

            $purchase->update([
                'supplier_purchase_order_id' => null,
            ]);

            return redirect()->back()->with('message', [
                'type' => SUCCESS_MSG,
                'title' => 'Success',
                'messages' => 'SPO Purchase deleted successfully.',
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
