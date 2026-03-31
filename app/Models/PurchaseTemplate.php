<?php
// cspell:ignore Webike meno
namespace App\Models;

use App\Models\Microzero\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseTemplate extends Model
{
    protected $table = 'purchase_templates';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'memo',
        'webike_logo_url',
        'webike_name',
        'webike_tax_id',
        'webike_street_address',
        'webike_district_sub_district',
        'webike_city_municipality',
        'webike_state_province',
        'webike_postal_code_zip_code',
        'webike_country',
        'webike_phone_number',
        'ship_to_name',
        'ship_to_street_address',
        'ship_to_district_sub_district',
        'ship_to_city_municipality',
        'ship_to_state_province',
        'ship_to_postal_code_zip_code',
        'ship_to_country',
        'ship_to_phone_number',
        'items',
        'is_display_total_amount',
        'is_display_webike_address',
        'note',
        'authorized_signature_url'
    ];

    protected $casts = [
        'items' => 'array',
        'is_display_total_amount' => 'boolean',
        'is_display_webike_address' => 'boolean'
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y/m/d H:i:s');
    }
}
