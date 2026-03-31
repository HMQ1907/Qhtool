<?php

namespace App\Http\Controllers\PurchaseTemplate;

use App\Actions\PurchaseTemplate\DestroyPurchaseTemplateAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DestroyTemplateController extends Controller
{
    public function __construct(protected DestroyPurchaseTemplateAction $destroyPurchaseTemplateAction) {}
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $ids = $request->input('ids');
            $result = $this->destroyPurchaseTemplateAction->handle($ids);

            return redirect()->back()->with('message', $result['message']);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->input(),
                'request' => $request,
            ]);

            throw $th;
        }
    }
}
