<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Actions\Api\Purchases\GetStatusPurchaseApiAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetStatusPurchaseApiController extends ApiController
{
    public function __construct(protected GetStatusPurchaseApiAction $getStatusPurchaseApiAction) {}

    public function __invoke(Request $request, string $identifyCode): JsonResponse
    {
        try {
            $response =  $this->getStatusPurchaseApiAction->handle($identifyCode);

            return $this->responseJsonApi($response);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
