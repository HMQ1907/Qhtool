<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Http\Controllers\Api\ApiController;
use App\Operations\Api\Purchase\CreatePurchaseApiOperation;
use App\Http\Requests\Api\Purchase\CreatePurchaseApiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreatePurchaseApiController extends ApiController
{
    public function __construct(protected CreatePurchaseApiOperation $createPurchaseApiOperation) {}

    public function __invoke(CreatePurchaseApiRequest $request): JsonResponse
    {
        try {
            $response = $this->createPurchaseApiOperation->handle($request->all());

            return $this->responseJsonApi($response);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
