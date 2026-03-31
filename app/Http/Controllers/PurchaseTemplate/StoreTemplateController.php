<?php

namespace App\Http\Controllers\PurchaseTemplate;

use App\Actions\PurchaseTemplate\StorePurchaseTemplateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseTemplate\StorePurchaseTemplateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreTemplateController extends Controller
{
    public function __construct(protected StorePurchaseTemplateAction $storePurchaseTemplateAction) {}

    public function __invoke(StorePurchaseTemplateRequest $request): RedirectResponse
    {
        try {
            $result = $this->storePurchaseTemplateAction->handle($request->all());

            if ($result) {
                return redirect()->back()->with('message', [
                    'type' => 'success',
                    'title' => 'Success',
                    'messages' => 'Purchase template created successfully!',
                ]);
            }

            return redirect()->back()->with('message', [
                'type' => 'error',
                'title' => 'Error',
                'messages' => 'Failed to create purchase template.',
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->input(),
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
