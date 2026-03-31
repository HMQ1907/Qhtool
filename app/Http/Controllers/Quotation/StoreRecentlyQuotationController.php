<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Actions\Quotations\StoreRecentlyQuotationAction;
use App\Http\Requests\Quotation\StoreRecentlyQuotationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreRecentlyQuotationController extends Controller
{
    public function __construct(protected StoreRecentlyQuotationAction $storeRecentlyQuotationAction) {}

    public function __invoke(StoreRecentlyQuotationRequest $request): RedirectResponse
    {
        try {
            $data = $request->all();

            if ($this->storeRecentlyQuotationAction->handle($data)) {
                return redirect()->back()->with('message', [
                    'type' => SUCCESS_MSG,
                    'title' => 'Success',
                    'messages' => "Update quotations successfully.",
                ]);
            }

            return redirect()->back()->withErrors([
                'type' => ERROR_MSG,
                'title' => 'Error',
                'messages' => 'Update quotations failed.',
            ], 'error');
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $request->all(),
            ]);

            throw $th;
        }
    }
}
