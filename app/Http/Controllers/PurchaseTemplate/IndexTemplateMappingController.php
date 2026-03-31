<?php

namespace App\Http\Controllers\PurchaseTemplate;

use App\Actions\PurchaseTemplate\GetTemplateMappingAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class IndexTemplateMappingController extends Controller
{
    public function __construct(protected GetTemplateMappingAction $getTemplateMappingAction) {}

    public function __invoke(Request $request): Response
    {
        try {
            $data = $this->getTemplateMappingAction->handle($request->input());

            return Inertia::render('PurchaseTemplate/TemplateMapping', [
                'templates' => $data['templates'],
                'suppliers' => $data['suppliers']
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
