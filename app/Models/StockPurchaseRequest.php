<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class StockPurchaseRequest extends BaseModel
{
    protected $table = 'stock_purchase_requests';

    protected $fillable = [
        'key',
        'content',
        'hash_code',
        'is_purchased',
        'executed_at',
    ];

    const STOCK_PURCHASE_HEADER_EXPORT = [
        'SKU (Required)',
        'Quantity (Required)',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(StockPurchase::class, 'request_id', 'id');
    }
}
