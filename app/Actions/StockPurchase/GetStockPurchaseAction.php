<?php

namespace App\Actions\StockPurchase;

use App\Models\StockPurchaseRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class GetStockPurchaseAction
{
    public function handle(array $input): LengthAwarePaginator
    {
        try {
            return QueryBuilder::for(StockPurchaseRequest::class)
                ->with(['purchases.supplier', 'purchases.product'])
                ->allowedFilters(AllowedFilter::callback('key', function ($query, $value) {
                    if (is_array($value)) {
                        $query->where(function ($q) use ($value) {
                            foreach ($value as $key) {
                                $q->orWhere('key', 'like', '%' . trim($key) . '%');
                            }
                        });
                    } else {
                        $query->where('key', 'like', '%' . trim($value) . '%');
                    }
                }))
                ->orderBy('id')
                ->paginate($input['per_page'] ?? DEFAULT_PER_PAGE);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
