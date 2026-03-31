<?php

namespace App\Models;

class Product extends BaseModel
{
    protected $table = 'products';
    protected $primaryKey = 'sku';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sku',
        'data',
        'hash',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'object',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->data->name ?? null;
    }

    public function getPriceAttribute(): string
    {
        return $this->data->price ?? null;
    }

    public function getEanCodeAttribute(): string
    {
        return $this->data->ean_code ?? null;
    }

    public function getMpnAttribute(): string
    {
        return $this->data->model_number ?? null;
    }

    public function getProductImageAttribute(): string
    {
        return $this->data->product_image ?? null;
    }

    public function getVariantsAttribute(): array
    {
        $variants = [];

        foreach ($this->data['variants'] as $variant) {
            $variants[array_key_first($variant)] = $variant[array_key_first($variant)];
        }

        return $variants;
    }
}
