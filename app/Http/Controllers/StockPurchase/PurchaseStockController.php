<?php

namespace App\Http\Controllers\StockPurchase;

use App\Actions\RabbitMQ\Messages\PurchaseStockMessage;
use App\Actions\RabbitMQ\ProducerAction;
use App\Http\Controllers\Controller;
use App\Models\StockPurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PurchaseStockController extends Controller
{
    protected string $page = 'Stock Purchase';

    public function __construct(
        protected ProducerAction $producerAction
    ) {}

    public function __invoke(Request $request, int $id): RedirectResponse
    {
        try {
            $stockPurchase = StockPurchaseRequest::with('purchases')->where('is_purchased', 0)->findOrFail($id);

            $this->producerAction->open();

            $stockPurchase->purchases->each(function ($purchase, $index) use ($stockPurchase) {
                $message = new PurchaseStockMessage($purchase, $stockPurchase->key, $index + 1);
                $this->producerAction->publishMessage($message->toJson());
            });

            $stockPurchase->update([
                'executed_at' => now(),
                'is_purchased' => 1,
            ]);

            createManagementToolHistory($this->page, "Purchase stock \"{$id}\" successfully.");

            return redirect()->back()->with('message', [
                'type' => 'success',
                'title' => 'Success',
                'messages' => "Purchase stock \"{$id}\" successfully.",
            ]);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'request' => $request,
                'id' => $id,
            ]);

            throw $th;
        } finally {
            $this->producerAction->close();
        }
    }
}
