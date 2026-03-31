<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPurchase extends BaseModel
{
    protected $table = 'stock_purchases';

    protected $fillable = [
        'request_id',
        'sku',
        'quantity',
        'supplier_code',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }
}
