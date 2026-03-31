<?php

namespace App\Actions\Purchases;

use App\Models\Pm\LocalSupplier;
use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderApproved;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApproveSPOAction
{
    public function handle(int $id): bool
    {
        try {
            $result = DB::transaction(function () use ($id) {
                $SPODetail = SupplierPurchaseOrder::with(['purchases', 'supplier.purchaseTemplate'])
                    ->where('status', SupplierPurchaseOrder::STATUS_PENDING)
                    ->findOrFail($id);

                $SPODetail->update([
                    'purchase_date' => now(),
                    'status' => SupplierPurchaseOrder::STATUS_APPROVED,
                ]);

                $SPODetail->purchases->each(function ($purchase) use ($SPODetail) {
                    $purchase->update([
                        'purchase_date' => now(),
                        'purchase_code' => $SPODetail->code,
                    ]);
                });

                $purchaseTemplate = $SPODetail->supplier->purchaseTemplate;
                $supplierData = LocalSupplier::where('supplier_code', $SPODetail->supplier_code)->first();

                SupplierPurchaseOrderApproved::create([
                    'supplier_purchase_order_id' => $id,

                    'webike_logo_url' => $purchaseTemplate?->webike_logo_url,
                    'webike_name' => $purchaseTemplate?->webike_name,
                    'webike_tax_id' => $purchaseTemplate?->webike_tax_id,
                    'webike_street_address' => $purchaseTemplate?->webike_street_address,
                    'webike_district_sub_district' => $purchaseTemplate?->webike_district_sub_district,
                    'webike_city_municipality' => $purchaseTemplate?->webike_city_municipality,
                    'webike_state_province' => $purchaseTemplate?->webike_state_province,
                    'webike_postal_code_zip_code' => $purchaseTemplate?->webike_postal_code_zip_code,
                    'webike_phone_number' => $purchaseTemplate?->webike_phone_number,
                    'webike_country' => $purchaseTemplate?->webike_country,

                    'supplier_name' => $supplierData->name ?? null,
                    'supplier_address' => $supplierData->supplier_address ?? null,
                    'supplier_address_district' => $supplierData->supplier_address_district ?? null,
                    'supplier_address_city' => $supplierData->supplier_address_city ?? null,
                    'supplier_address_state' => $supplierData->supplier_address_state ?? null,
                    'supplier_address_postal_code' => $supplierData->supplier_address_postal_code ?? null,
                    'supplier_address_country' => $supplierData->supplier_address_country ?? null,
                    'supplier_phone' => $supplierData->supplier_phone ?? null,
                    'tax_id' => $supplierData->tax_id ?? null,

                    'ship_to_name' => $purchaseTemplate?->ship_to_name,
                    'ship_to_street_address' => $purchaseTemplate?->ship_to_street_address,
                    'ship_to_district_sub_district' => $purchaseTemplate?->ship_to_district_sub_district,
                    'ship_to_city_municipality' => $purchaseTemplate?->ship_to_city_municipality,
                    'ship_to_state_province' => $purchaseTemplate?->ship_to_state_province,
                    'ship_to_postal_code_zip_code' => $purchaseTemplate?->ship_to_postal_code_zip_code,
                    'ship_to_country' => $purchaseTemplate?->ship_to_country,
                    'ship_to_phone_number' => $purchaseTemplate?->ship_to_phone_number,
                    'ship_to_country' => $purchaseTemplate?->ship_to_country,
                    'items' => $purchaseTemplate?->items ?? null,
                    'is_display_total_amount' => $purchaseTemplate?->is_display_total_amount ?? null,
                    'is_display_webike_address' => $purchaseTemplate?->is_display_webike_address ?? null,
                    'note' => $purchaseTemplate?->note ?? null,
                    'authorized_signature_url' => $purchaseTemplate?->authorized_signature_url ?? null,
                ]);

                createManagementToolHistory('SPO Purchases', 'Approve SPO: ' . $SPODetail->code . ' successfully');
                return true;
            }, NUMBER_TRANSACTION);

            return $result;
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'id' => $id,
            ]);

            throw $th;
        }
    }
}
