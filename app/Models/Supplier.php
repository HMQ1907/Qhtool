<?php

namespace App\Models;

use App\Models\Microzero\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends BaseModel
{
    protected $table = 'suppliers';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'data',
        'hash',
        'purchase_template_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'object',
    ];
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_supplier', 'supplier_code', 'sku');
    }

    public function purchaseTemplate(): BelongsTo
    {
        return $this->belongsTo(PurchaseTemplate::class, 'purchase_template_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault([
            'name' => 'System',
        ]);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->withDefault([
            'name' => 'System',
        ]);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y/m/d H:i:s');
    }
}
