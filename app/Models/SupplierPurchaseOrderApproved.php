<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPurchaseOrderApproved extends Model
{
    protected $table = 'supplier_purchase_orders_approved';

    protected $fillable = [
        'supplier_purchase_order_id',
        'webike_logo_url',
        'webike_name',
        'webike_tax_id',
        'webike_street_address',
        'webike_district_sub_district',
        'webike_city_municipality',
        'webike_state_province',
        'webike_postal_code_zip_code',
        'webike_phone_number',
        'webike_country',
        'supplier_name',
        'supplier_address',
        'supplier_address_district',
        'supplier_address_city',
        'supplier_address_state',
        'supplier_address_postal_code',
        'supplier_address_country',
        'supplier_phone',
        'tax_id',
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
        'authorized_signature_url',
    ];

    protected $casts = [
        'items' => 'array',
        'is_display_total_amount' => 'boolean',
        'is_display_webike_address' => 'boolean'
    ];

    public function supplierPurchaseOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierPurchaseOrder::class, 'supplier_purchase_order_id', 'id');
    }
}
