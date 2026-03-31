<?php

namespace App\Http\Controllers\PurchaseTemplate;

use App\Actions\PurchaseTemplate\GetTemplateAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class IndexTemplateController extends Controller
{
    public function __construct(protected GetTemplateAction $getTemplateAction) {}

    public function __invoke(Request $request): Response
    {
        try {
            $purchaseTemplates = $this->getTemplateAction->handle($request->input());

            return Inertia::render('PurchaseTemplate/Template', [
                'purchaseTemplates' => $purchaseTemplates,
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
