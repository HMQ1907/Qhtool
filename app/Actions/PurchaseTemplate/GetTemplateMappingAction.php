<?php
// cspell:disable Spatie
namespace App\Actions\PurchaseTemplate;

use App\Models\Pm\LocalSupplier;
use App\Models\PurchaseTemplate;
use Throwable;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetTemplateMappingAction
{
    public function handle(array $input): array
    {
        try {
            $suppliersActive = LocalSupplier::where('status', 2)->where('active', 1)->whereIn('p_country', ['TH', 'EU'])->get()->pluck('supplier_code');

            $suppliers = QueryBuilder::for(Supplier::class)
                ->with(['purchaseTemplate:id,name', 'updatedBy:id,name'])
                ->whereIn('code', $suppliersActive)
                ->allowedFilters([AllowedFilter::exact('code')])
                ->orderBy("code")
                ->paginate($input['per_page'] ?? 50)
                ->onEachSide(EACH_SIZE_PAGINATION);

            $templates = QueryBuilder::for(PurchaseTemplate::class)
                ->select('id', 'name')
                ->orderBy("id")
                ->get();

            return [
                'suppliers' => $suppliers,
                'templates' => $templates
            ];
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
