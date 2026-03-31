<?php

namespace App\Http\Controllers\PurchaseTemplate;

use Illuminate\Http\RedirectResponse;
use App\Actions\PurchaseTemplate\UpdatePurchaseTemplateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseTemplate\UpdatePurchaseTemplateRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateTemplateController extends Controller
{
    public function __construct(protected UpdatePurchaseTemplateAction $updatePurchaseTemplateAction) {}

    public function __invoke(UpdatePurchaseTemplateRequest $request, int $id): RedirectResponse
    {
        try {
            $result = $this->updatePurchaseTemplateAction->handle($request->all(), $id);

            if ($result) {
                return redirect()->back()->with('message', [
                    'type' => 'success',
                    'title' => 'Success',
                    'messages' => 'Purchase template updated successfully!',
                ]);
            }

            return redirect()->back()->with('message', [
                'type' => 'error',
                'title' => 'Error',
                'messages' => 'Failed to update purchase template.',
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'id' => $id,
                'input' => $request->input(),
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
