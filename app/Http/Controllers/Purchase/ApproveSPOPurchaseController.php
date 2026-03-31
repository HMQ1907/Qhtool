<?php

namespace App\Http\Controllers\Purchase;

use App\Actions\Purchases\ApproveSPOAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApproveSPOPurchaseController extends Controller
{
    public function __construct(
        protected ApproveSPOAction $approveSPOAction
    ) {}

    public function __invoke(Request $request, int $id)
    {
        try {
            $result = $this->approveSPOAction->handle($id);

            if ($result) {
                return redirect()->back()->with('message', [
                    'type' => SUCCESS_MSG,
                    'title' => 'Success',
                    'messages' => "Approved SPO successfully.",
                ]);
            }

            return redirect()->back()->with('message', [
                'type' => ERROR_MSG,
                'title' => 'Error',
                'messages' => 'SPO approved failed.',
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
