<?php

namespace App\Actions\Quotations;

use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Collection;
use Throwable;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilderRequest;

class GetRecentlyQuotationAction
{
    public function handle(array $input = [])
    {
        try {
            $pageSize = $input['per_page'] ?? DEFAULT_PER_PAGE;
            $listData = $this->getSupplierCodes((int) $pageSize, $input);
            $supplierCodes = $listData->pluck('supplier_code')->toArray();
            $listDataArray = $listData->toArray();
            $items = $this->getItems($supplierCodes);
            $groupData = $items->groupBy('supplier_code')->toArray();
            $listDataArray['data'] = $groupData;

            return $listDataArray;
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }

    protected function getSupplierCodes(int $pageSize, array $input): LengthAwarePaginator
    {
        $request = new QueryBuilderRequest(['filter' => $input['filter'] ?? []]);
        $queryBuilder = QueryBuilder::for(Quotation::class, $request);

        return $queryBuilder->select('supplier_code')
            ->allowedFilters([
                AllowedFilter::exact('supplier_code'),
            ])
            ->where('is_canceled', 0)
            ->where(function ($query) {
                $query->whereNot('checked', '1')
                    ->orWhereNull('delivery_days');
            })->orderBy('supplier_code')
            ->groupBy('supplier_code')
            ->paginate($pageSize)
            ->onEachSide(EACH_SIZE_PAGINATION);
    }

    protected function getItems(array $supplierCodes): Collection
    {
        return Quotation::where('is_canceled', 0)
            ->with(['product', 'supplier', 'orders:increment_id,order_scm_code'])
            ->where(function ($query) {
                $query->whereNot('checked', '1')
                    ->orWhereNull('delivery_days');
            })->orderByRaw("supplier_code asc, CASE WHEN scm_code REGEXP '^[A-Za-z ]+$' THEN 0 ELSE 1 END")
            ->whereIn('supplier_code', $supplierCodes)
            ->get();
    }
}
