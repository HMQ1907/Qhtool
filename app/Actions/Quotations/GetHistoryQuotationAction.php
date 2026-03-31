<?php

namespace App\Actions\Quotations;

use Throwable;
use App\Models\Quotation;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\QueryBuilderRequest;

class GetHistoryQuotationAction
{
    public function handle(array $input = [])
    {
        try {
            $request = new QueryBuilderRequest(['filter' => $input['filter'] ?? []]);
            $queryBuilder = QueryBuilder::for(Quotation::class, $request);

            return $queryBuilder->with(['product', 'supplier'])
                ->allowedFilters([
                    AllowedFilter::exact('sku'),
                    AllowedFilter::exact('identify_code'),
                    AllowedFilter::exact('supplier_code'),
                ])
                ->where('checked', Quotation::CHECKED)
                ->orderBy('supplier_code')
                ->orderByDesc('id');
        } catch (Throwable $th) {
            Log::debug(__METHOD__, ['input' => $input]);
            throw $th;
        }
    }
}
