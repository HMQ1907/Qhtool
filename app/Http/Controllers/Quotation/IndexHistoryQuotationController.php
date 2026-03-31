<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Actions\Quotations\GetHistoryQuotationAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class IndexHistoryQuotationController extends Controller
{
    public function __construct(protected GetHistoryQuotationAction $getHistoryQuotationAction) {}

    public function __invoke(Request $request): Response
    {
        try {
            $filter = $request->input();

            $quotations = $filter ? $this->getHistoryQuotationAction->handle($filter)->paginate($request->input('per_page', DEFAULT_PER_PAGE))
                ->onEachSide(EACH_SIZE_PAGINATION) : [];

            return Inertia::render('Quotations/History', [
                'quotations' => $quotations
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
