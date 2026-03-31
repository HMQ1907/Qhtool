<?php

namespace App\Models;

use App\Models\EgZero\Orders;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends BaseModel
{
    const CHECKED = 1;

    use HasFactory;

    const TIMEDIFF = 1 * 60;

    protected $table = 'quotations';

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
        'delivery_days',
        'note',
        'checked',
        'scm_code',
        'is_canceled',
    ];

    protected $casts = [
        'unit_price' => 'integer',
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

    public function isCompleted(): bool
    {
        $current_time = time();
        $new_time = $current_time - self::TIMEDIFF;

        return $this->checked && $this->delivery_days && $this->updated_at->timestamp < $new_time;
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y/m/d H:i:s');
    }
}
