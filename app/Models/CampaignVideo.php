<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class CampaignVideo extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'hashtags' => 'array',
        'product_images' => 'array',
        'generated_product_images' => 'array',
        'duration_seconds' => 'integer',
        'external_url_expires_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
