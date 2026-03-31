<?php

namespace App\Http\Controllers\PurchaseTemplate;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EditTemplateController extends Controller
{
    public function __invoke(Request $request, int $id): JsonResponse
    {
        try {
            $purchaseTemplate = PurchaseTemplate::findOrFail($id);

            return responseWithJson($purchaseTemplate, 'success', 200);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->input(),
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
