<?php

namespace App\Http\Controllers\Purchase;

use App\Actions\Purchases\StoreRecentlyPurchaseAction;
use App\Actions\Sequences\GetSequenceAction;
use App\Actions\Purchases\StoreSupplierPurchaseOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StoreRecentlyPurchaseRequest;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreRecentlyPurchaseController extends Controller
{
    public function __construct(
        protected StoreSupplierPurchaseOrderAction $storeSupplierPurchaseOrderAction,
        protected StoreRecentlyPurchaseAction $storeRecentlyPurchaseAction,
        protected GetSequenceAction $getSequenceAction
    ) {}

    public function __invoke(StoreRecentlyPurchaseRequest $request)
    {
        try {
            $data = $request->all();

            $supplierCode = $data['purchases'][0]['supplier_code'];
            $supplier = Supplier::where('code', $supplierCode)->first();

            if (!$supplier->purchase_template_id) {
                return redirect()->back()->with('message', [
                    'type' => ERROR_MSG,
                    'title' => 'Error',
                    'messages' => "Supplier does not have a purchase template.",
                ]);
            }

            $result = DB::transaction(function () use ($data) {
                if ($data['purchases'][0]['mode'] == 'create') {
                    $sequence = $this->getSequenceAction->handle(SUPPLIER_PURCHASE);
                    $spo = $this->storeSupplierPurchaseOrderAction->handle([
                        'code' => $sequence,
                        'supplier_code' => $data['purchases'][0]['supplier_code'],
                    ]);
                    $data['purchases'][0]['spo_id'] = $spo->id;
                }

                $this->storeRecentlyPurchaseAction->handle($data);
                return true;
            }, NUMBER_TRANSACTION);

            if ($result) {
                return redirect()->back()->with('message', [
                    'type' => SUCCESS_MSG,
                    'title' => 'Success',
                    'messages' => "Update SPO successfully.",
                ]);
            }
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
