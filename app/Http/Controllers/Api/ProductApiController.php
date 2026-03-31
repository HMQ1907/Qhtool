<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ProductApiUpdateRequest;
use App\Operations\Api\UpdateProductApiOperation;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductApiController extends ApiController
{
    public function __construct(protected UpdateProductApiOperation $updateProductApiOperation) {}

    public function __invoke(ProductApiUpdateRequest $request)
    {
        try {
            $response = $this->updateProductApiOperation->handle($request->get('sku'));

            return $this->responseJsonApi($response);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
