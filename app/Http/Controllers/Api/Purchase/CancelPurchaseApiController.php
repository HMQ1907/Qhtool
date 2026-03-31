<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Actions\Api\Purchases\CancelPurchaseApiAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelPurchaseApiController extends ApiController
{
    public function __construct(protected CancelPurchaseApiAction $cancelPurchaseApiAction) {}

    public function __invoke(Request $request, string $identifyCode): JsonResponse
    {
        try {
            $response = $this->cancelPurchaseApiAction->handle($identifyCode);

            return $this->responseJsonApi($response);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
