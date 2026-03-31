<?php

namespace App\Actions\Purchases;

use Throwable;
use App\Models\SupplierPurchaseOrder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class GetSPOPurchaseAction
{
    public function handle(array $input): LengthAwarePaginator
    {
        try {
            return QueryBuilder::for(SupplierPurchaseOrder::class)->with([
                'purchases',
                'supplier',
            ])
                ->allowedFilters([
                    AllowedFilter::exact('supplier_code'),
                    AllowedFilter::exact('code'),
                    AllowedFilter::exact('status'),
                    AllowedFilter::operator(name: 'begin_date', filterOperator: FilterOperator::GREATER_THAN_OR_EQUAL, internalName: "purchase_date"),
                    AllowedFilter::operator(name: 'end_date', filterOperator: FilterOperator::LESS_THAN_OR_EQUAL, internalName: "purchase_date"),
                ])
                ->orderByDesc("id")
                ->paginate($input['per_page'] ?? DEFAULT_PER_PAGE)
                ->onEachSide(EACH_SIZE_PAGINATION);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
