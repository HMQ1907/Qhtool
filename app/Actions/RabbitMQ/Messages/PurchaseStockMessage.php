<?php

namespace App\Actions\RabbitMQ\Messages;

use App\Models\StockPurchase;

class PurchaseStockMessage extends BaseMessage
{
    public function __construct(
        protected StockPurchase $purchase,
        protected string $requestKey,
        protected int $keyIndex
    ) {
        parent::__construct();
    }

    protected function createMessage(): void
    {
        $this->message = [
            'action' => 'create',
            'source' => 'eg-strategy',
            'instance' => [
                'id' => $this->requestKey . '-' . $this->keyIndex,
                'items' => [$this->buildItem()],
            ],
        ];
    }

    protected function buildItem(): array
    {
        return [
            'id' => $this->purchase->id,
            'sku' => $this->purchase->sku,
            'quantity' => (int) $this->purchase->quantity,
            'estimate_supplier_key' => $this->purchase->supplier_code,
        ];
    }
}
