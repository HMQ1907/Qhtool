<?php

namespace App\Http\Controllers\Api\Quotation;

use App\Actions\Api\Quotations\GetStatusQuotationApiAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetStatusQuotationApiController extends ApiController
{
    public function __construct(protected GetStatusQuotationApiAction $getStatusQuotationApiAction) {}

    public function __invoke(Request $request, string $identifyCode): JsonResponse
    {
        try {
            $response =  $this->getStatusQuotationApiAction->handle($identifyCode);

            return $this->responseJsonApi($response);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
