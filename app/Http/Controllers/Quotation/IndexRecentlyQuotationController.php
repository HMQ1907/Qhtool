<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Actions\Quotations\GetRecentlyQuotationAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class IndexRecentlyQuotationController extends Controller
{
    public function __construct(protected GetRecentlyQuotationAction $getRecentlyQuotationAction) {}

    public function __invoke(Request $request): Response
    {
        try {
            $quotations = $this->getRecentlyQuotationAction->handle($request->input());
            return Inertia::render('Quotations/Recently', [
                'quotations' => $quotations,
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'request' => $request,
                'input' => $request->input(),
            ]);

            throw $th;
        }
    }
}
