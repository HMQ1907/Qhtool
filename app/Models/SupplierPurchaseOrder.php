<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierPurchaseOrder extends Model
{
    const STATUS_APPROVED = 1;
    const STATUS_PENDING = 0;

    protected $table = 'supplier_purchase_orders';

    protected $fillable = [
        'code',
        'supplier_code',
        'purchase_date',
        'status',
    ];

    const SPO_DETAIL_HEADER_EXPORT = [
        'ID',
        'PO No.',
        'Purchase Date',
        'SUPPLIER',
        'SKU',
        'Part No.',
        'DESCRIPTION',
        'VARIANT',
        'QTY',
        'SCM Code',
        'Order No.',
        'UNIT PRICE',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_purchase_order_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
    }
}
