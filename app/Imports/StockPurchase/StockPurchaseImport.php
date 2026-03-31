<?php

namespace App\Imports\StockPurchase;

use App\Actions\StoreProductsAction;
use App\Actions\StoreSuppliersAction;
use App\Actions\Webike\GetOrderProductsAction;
use App\Imports\BaseImport;
use App\Models\StockPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Row;
use Throwable;
use UnexpectedValueException;

class StockPurchaseImport extends BaseImport
{
    protected GetOrderProductsAction $getOrderProductsAction;
    protected StoreProductsAction $storeProductsAction;
    protected StoreSuppliersAction $storeSuppliersAction;

    public function __construct(
        protected int $stockPurchaseRequestId,
        protected int $userActivityId,
        protected int $userId = 0,
    ) {
        parent::__construct($userActivityId, $userId);
        $this->getOrderProductsAction = app()->make(GetOrderProductsAction::class);
        $this->storeProductsAction = app()->make(StoreProductsAction::class);
        $this->storeSuppliersAction = app()->make(StoreSuppliersAction::class);
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row = $row->toArray();

        try {
            $product = $this->getOrderProductsAction->handle($row[0]);
            if (empty($product)) {
                throw new UnexpectedValueException("Product with sku '{$row[0]}' not found.");
            } else {
                DB::transaction(function () use ($product, $row) {
                    foreach ($product['suppliers'] as $supplier) {
                        $this->storeSuppliersAction->handle($supplier);
                    }
                    $this->storeProductsAction->handle($product['product']);
                    StockPurchase::create([
                        'request_id' => $this->stockPurchaseRequestId,
                        'supplier_code' => $product['suppliers'][0]['key'],
                        'sku' => $row[0],
                        'quantity' => $row[1],
                    ]);
                });

                $this->totalSuccess++;
            }
        } catch (Throwable $th) {
            Log::error(__METHOD__, [
                'row' => $row,
                'error' => $th->getMessage(),
            ]);
            $this->totalFailed++;
            $this->errorRecords[] = [
                'user_activity_import_result_id' => $this->userActivityImportResult->id,
                'identify_name' => !isEmpty($row[0]) ? 'SKU' : 'Line',
                'identify_value' => !isEmpty($row[0]) ? $row[0] : $rowIndex,
                'error_message' => $th->getMessage(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $this->totalRecords++;
    }
}
