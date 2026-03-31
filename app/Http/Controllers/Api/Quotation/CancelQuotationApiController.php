<?php

namespace App\Http\Controllers\Api\Quotation;

use App\Actions\Api\Quotations\CancelQuotationApiAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelQuotationApiController extends ApiController
{
    public function __construct(protected CancelQuotationApiAction $cancelQuotationApiAction) {}

    public function __invoke(Request $request, string $identifyCode): JsonResponse
    {
        try {
            Log::info('cancel quotation: ' . $identifyCode);
            $response =  $this->cancelQuotationApiAction->handle($identifyCode);

            return $this->responseJsonApi($response);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
