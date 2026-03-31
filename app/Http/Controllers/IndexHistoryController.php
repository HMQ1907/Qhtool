<?php

namespace App\Http\Controllers;

use App\Actions\History\GetCurrentPageHistoryAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class IndexHistoryController extends Controller
{
    public function __construct(protected GetCurrentPageHistoryAction $getCurrentPageHistoryAction) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = $this->getCurrentPageHistoryAction->handle(
                input: $request->input(),
                relation: ['createdBy', 'updatedBy', 'userActivityImportResult.userActivityImportErrors']
            );
            return responseWithJson($data, SUCCESS_MSG, DONE_CODE);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
