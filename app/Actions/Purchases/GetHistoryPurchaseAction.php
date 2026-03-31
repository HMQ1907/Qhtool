<?php

namespace App\Actions\Purchases;

use Throwable;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilderRequest;

class GetHistoryPurchaseAction
{
    public function handle(array $input): QueryBuilder
    {
        try {
            $request = new QueryBuilderRequest(['filter' => $input['filter'] ?? []]);
            $queryBuilder = QueryBuilder::for(Purchase::class, $request);
            return $queryBuilder->with([
                'product',
                'supplier',
                'orders:increment_id,order_scm_code',
            ])
                ->allowedFilters([
                    AllowedFilter::exact('sku'),
                    AllowedFilter::exact('identify_code'),
                    AllowedFilter::exact('supplier_code'),
                    AllowedFilter::exact('scm_code'),
                    AllowedFilter::exact('purchase_code'),
                    AllowedFilter::operator(name: 'begin_date', filterOperator: FilterOperator::GREATER_THAN_OR_EQUAL, internalName: "purchase_date"),
                    AllowedFilter::operator(name: 'end_date', filterOperator: FilterOperator::LESS_THAN_OR_EQUAL, internalName: "purchase_date"),
                ])
                ->whereNotNull("purchase_code")
                ->whereNotNull("purchase_date")
                ->orderByDesc("id")
                ->orderBy("supplier_code");
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
