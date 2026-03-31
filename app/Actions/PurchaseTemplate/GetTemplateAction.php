<?php

namespace App\Actions\PurchaseTemplate;

use Throwable;
use App\Models\PurchaseTemplate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetTemplateAction
{
    public function handle(array $input): LengthAwarePaginator
    {
        try {
            return QueryBuilder::for(PurchaseTemplate::class)
                ->with('updatedBy:id,name')
                ->allowedFilters([AllowedFilter::exact('name')])
                ->orderBy("id")->paginate($input['per_page'] ?? DEFAULT_PER_PAGE)->onEachSide(EACH_SIZE_PAGINATION);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
