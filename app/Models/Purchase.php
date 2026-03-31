<?php

namespace App\Models;

use App\Models\EgZero\Orders;
use App\Models\Wfm\Orders as WfmOrders;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Purchase extends BaseModel
{
    use HasFactory;

    protected $table = 'purchases';

    protected $casts = [
        'unit_price' => 'integer',
    ];

    protected $fillable = [
        'id',
        'identify_code',
        'request_from',
        'reference_code',
        'sku',
        'quantity',
        'supplier_code',
        'unit_price',
        'currency',
        'delivery_on',
        'received_on',
        'received_quantity',
        'delivery_days',
        'note',
        'deleted_at',
        'scm_code',
        'delivery_days',
        'purchase_date',
        'purchase_code',
        'is_canceled',
        'supplier_purchase_order_id',
        'is_discontinued',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sku');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
    }

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'scm_code', 'increment_id');
    }

    public function wfmOrders(): BelongsTo
    {
        return $this->belongsTo(WfmOrders::class, 'scm_code', 'foreign_key');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y/m/d');
    }

    public function supplierPurchaseOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierPurchaseOrder::class, 'supplier_purchase_order_id', 'id');
    }
}
