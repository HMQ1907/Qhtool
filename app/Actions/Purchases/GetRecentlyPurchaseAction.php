<?php
// Cspell:disable webike
namespace App\Actions\Purchases;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Purchase;
use App\Models\EgZero\Orders;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;
use Spatie\QueryBuilder\QueryBuilderRequest;

class GetRecentlyPurchaseAction
{
    public function handle($request = [])
    {
        $pageSize = $request['per_page'] ?? DEFAULT_PER_PAGE;
        $filters = $request['filter'] ?? $request;

        $listData = $this->getSupplierCodes((int) $pageSize, $request);

        $supplierCodes = $listData
            ->pluck('supplier_code')
            ->toArray();
        $listDataArray = $listData->toArray();

        $purchases = $this->getItems($filters, $supplierCodes);
        $purchases = $this->filterWebikeOrder($purchases, $filters);
        $groupData = $purchases->groupBy('supplier_code')->toArray();

        $listDataArray['data'] = $groupData;

        return $listDataArray;
    }

    protected function getRelationship($request = [])
    {
        $defaultRelationship = [
            'product:sku,data',
            'supplier:code,data',
        ];

        if (isset($request['is_webike_order']) && !in_array($request['is_webike_order'], [Orders::ALL_ORDER, Orders::STOCK_PURCHASE])) {
            $defaultRelationship['orders'] = function ($query) use ($request) {
                $query->where('is_webike_order', $request['is_webike_order']);

                if (isset($request['customer_id']) && $request['customer_id'] != "") {
                    $query->where('customer_id', $request['customer_id']);
                }

                $query->select('increment_id', 'order_scm_code');
            };
        } else {
            $defaultRelationship[] = 'orders:increment_id,order_scm_code';
        }

        if (isset($request['is_webike_order']) && $request['is_webike_order'] == Orders::STOCK_PURCHASE) {
            $defaultRelationship['wfmOrders'] = function ($query) {
                $query->where('type', Orders::STOCK_PURCHASE);
            };
        }

        return $defaultRelationship;
    }

    protected function filterWebikeOrder($query, $request)
    {
        if (isset($request['is_webike_order']) && !in_array($request['is_webike_order'], [Orders::ALL_ORDER, Orders::STOCK_PURCHASE])) {
            $query = $query->filter(function ($item) {
                return $item->orders != null;
            });
        }

        if (isset($request['is_webike_order']) && $request['is_webike_order'] == Orders::STOCK_PURCHASE) {
            $query = $query->filter(function ($item) {
                return $item->wfmOrders != null;
            });
        }

        return $query;
    }

    protected function getSupplierCodes(int $pageSize, array $input): LengthAwarePaginator
    {
        $request = new QueryBuilderRequest(['filter' => $input['filter'] ?? []]);
        $queryBuilder = QueryBuilder::for(Purchase::class, $request);
        return $queryBuilder->select('supplier_code')
            ->allowedFilters([
                AllowedFilter::exact('supplier_code'),
                AllowedFilter::callback('is_webike_order', function () {}),
                AllowedFilter::callback('customer_id', function () {}),
                AllowedFilter::callback('code', function () {}),
            ])
            ->where('is_canceled', 0)
            ->whereNull('purchase_code')
            ->groupBy('supplier_code')
            ->orderBy('supplier_code')
            ->paginate($pageSize)
            ->onEachSide(EACH_SIZE_PAGINATION);
    }

    protected function getItems($filters, array $supplierCodes): Collection
    {
        try {
            $relationship = $this->getRelationship($filters);

            $query = QueryBuilder::for(Purchase::class)
                ->with('supplierPurchaseOrder')
                ->where('is_canceled', 0)
                ->whereNull('purchase_code')
                ->orderBy('supplier_code')
                ->orderBy('id')
                ->whereIn('supplier_code', $supplierCodes)
                ->with($relationship);

            if (isset($filters['code']) && $filters['code'] != '') {
                $codes = explode(',', $filters['code']);
                $query->whereHas('supplierPurchaseOrder', function ($query) use ($codes) {
                    $query->whereIn('code', $codes);
                });
            }

            return $query->get();
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'supplierCodes' => $supplierCodes,
                'filters' => $filters,
            ]);

            throw $th;
        }
    }
}
