<?php

namespace App\Http\Controllers\Api\Quotation;

use App\Http\Controllers\Api\ApiController;
use App\Operations\Api\Quotation\CreateQuotationApiOperation;
use App\Http\Requests\Api\Quotation\CreateQuotationApiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateQuotationApiController extends ApiController
{
    public function __construct(protected CreateQuotationApiOperation $createQuotationApiOperation) {}

    public function __invoke(CreateQuotationApiRequest $request): JsonResponse
    {
        try {
            $response = $this->createQuotationApiOperation->handle($request->all());

            return $this->responseJsonApi($response);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
