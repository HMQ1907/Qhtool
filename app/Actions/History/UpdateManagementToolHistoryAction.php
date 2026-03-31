<?php

namespace App\Actions\History;

use App\Models\ManagementToolHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateManagementToolHistoryAction
{
    public function handle(int $id, string $operation): ManagementToolHistory
    {
        try {
            $managementToolHistory = ManagementToolHistory::findOrFail($id);

            $managementToolHistory->update([
                'operation' => $operation,
                'updated_by' => Auth::id() ?? 0,
            ]);

            return $managementToolHistory;
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'id' => $id,
                'operation' => $operation,
            ]);

            throw $th;
        }
    }
}
