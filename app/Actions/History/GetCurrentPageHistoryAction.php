<?php

namespace App\Actions\History;

use App\Models\ManagementToolHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetCurrentPageHistoryAction
{
    public function handle(array $input, array $select = ['*'], array $relation = []): LengthAwarePaginator
    {
        try {
            return ManagementToolHistory::with($relation)->select($select)->where('module', $input['module'])
                ->orderByDesc('created_at')->paginate($input['per_page'] ?? 20)
                ->withQueryString();
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
                'select' => $select,
                'relation' => $relation,
            ]);

            throw $th;
        }
    }
}
