<?php

namespace App\Actions\History;

use App\Models\ManagementToolHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateManagementToolHistoryAction
{
    public function handle(string $page, string $operation): ManagementToolHistory
    {
        try {
            return ManagementToolHistory::create([
                'module' => $page,
                'operation' => $operation,
                'created_by' => Auth::id() ?? 0,
                'updated_by' => Auth::id() ?? 0,
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'page' => $page,
                'operation' => $operation,
            ]);

            throw $th;
        }
    }
}
