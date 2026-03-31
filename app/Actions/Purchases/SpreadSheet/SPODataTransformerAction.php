<?php
// cspell:disable webike
namespace App\Actions\Purchases\SpreadSheet;

use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderApproved;
use App\Models\Purchase;
use App\Models\EgZero\Orders;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use stdClass;

class SPODataTransformerAction
{
    public function handle($data): object
    {
        if ($data instanceof SupplierPurchaseOrderApproved) {
            return $this->transformApprovedData($data);
        }

        if ($data instanceof SupplierPurchaseOrder) {
            return $this->transformPendingData($data);
        }

        throw new InvalidArgumentException('Unsupported data type for transformation');
    }

    private function transformApprovedData(SupplierPurchaseOrderApproved $approvedData): object
    {
        $purchaseTemplate = new stdClass();
        $purchaseTemplate->webike_logo_url = $approvedData->webike_logo_url;
        $purchaseTemplate->webike_name = $approvedData->webike_name;
        $purchaseTemplate->webike_tax_id = $approvedData->webike_tax_id;
        $purchaseTemplate->webike_street_address = $approvedData->webike_street_address;
        $purchaseTemplate->webike_district_sub_district = $approvedData->webike_district_sub_district;
        $purchaseTemplate->webike_city_municipality = $approvedData->webike_city_municipality;
        $purchaseTemplate->webike_state_province = $approvedData->webike_state_province;
        $purchaseTemplate->webike_postal_code_zip_code = $approvedData->webike_postal_code_zip_code;
        $purchaseTemplate->webike_phone_number = $approvedData->webike_phone_number;
        $purchaseTemplate->webike_country = $approvedData->webike_country;
        $purchaseTemplate->ship_to_name = $approvedData->ship_to_name;
        $purchaseTemplate->ship_to_street_address = $approvedData->ship_to_street_address;
        $purchaseTemplate->ship_to_district_sub_district = $approvedData->ship_to_district_sub_district;
        $purchaseTemplate->ship_to_city_municipality = $approvedData->ship_to_city_municipality;
        $purchaseTemplate->ship_to_state_province = $approvedData->ship_to_state_province;
        $purchaseTemplate->ship_to_postal_code_zip_code = $approvedData->ship_to_postal_code_zip_code;
        $purchaseTemplate->ship_to_country = $approvedData->ship_to_country;
        $purchaseTemplate->ship_to_phone_number = $approvedData->ship_to_phone_number;
        $purchaseTemplate->items = $approvedData->items;
        $purchaseTemplate->is_display_total_amount = $approvedData->is_display_total_amount;
        $purchaseTemplate->is_display_webike_address = $approvedData->is_display_webike_address;
        $purchaseTemplate->note = $approvedData->note;
        $purchaseTemplate->authorized_signature_url = $approvedData->authorized_signature_url;

        $supplier = new stdClass();
        $supplier->code = $approvedData->supplierPurchaseOrder->supplier_code ?? '';
        $supplier->name = $approvedData->supplierPurchaseOrder->supplier->data->name ?? '';
        $supplier->purchaseTemplate = $purchaseTemplate;

        $spo = new stdClass();
        $spo->id = $approvedData->supplier_purchase_order_id;
        $spo->code = $approvedData->supplierPurchaseOrder->code ?? '';
        $spo->supplier_code = $approvedData->supplierPurchaseOrder->supplier_code ?? '';
        $spo->purchase_date = $approvedData->supplierPurchaseOrder->purchase_date ?? '';
        $spo->created_at = $approvedData->supplierPurchaseOrder->created_at ?? '';
        $spo->updated_at = $approvedData->supplierPurchaseOrder->updated_at ?? '';
        $spo->status = $approvedData->supplierPurchaseOrder->status ?? '';
        $spo->supplier = $supplier;

        $originalSpo = $approvedData->supplierPurchaseOrder;

        $purchaseGroup = $this->transformPurchaseGroup($originalSpo);

        $spo->purchases = $purchaseGroup;

        return $spo;
    }

    protected function transformPendingData(SupplierPurchaseOrder $spoData): object
    {
        $purchaseGroup = $this->transformPurchaseGroup($spoData);

        $spoData->purchases = $purchaseGroup;

        return $spoData;
    }

    protected function transformPurchaseGroup(SupplierPurchaseOrder $spo): object
    {
        $purchaseGroup = Purchase::with(['product'])
            ->select([
                'sku',
                DB::raw('SUM(quantity) as quantity'),
                DB::raw('MAX(unit_price) as unit_price'),
                DB::raw("GROUP_CONCAT(CONCAT(scm_code, '(', quantity, ')') SEPARATOR '\n') as order_number"),
                DB::raw("SUM(quantity*unit_price) as amount")
            ])
            ->where('supplier_purchase_order_id', $spo->id)
            ->groupBy('sku')
            ->get();

        $allScmCodes = [];

        foreach ($purchaseGroup as $item) {
            foreach (explode("\n", $item->order_number) as $line) {
                $allScmCodes[] = explode('(', $line)[0];
            }
        }

        $orders = Orders::whereIn('increment_id', $allScmCodes)
            ->pluck('order_scm_code', 'increment_id');

        $purchaseGroup->each(function ($item) use ($orders) {
            $scmCodes = [];

            foreach (explode("\n", $item->order_number) as $line) {
                [$scmCode, $qty] = explode('(', rtrim($line, ')'));
                if (isset($orders[$scmCode])) {
                    $scmCodes[] = "{$orders[$scmCode]}({$qty})";
                }
            }

            $item->part_no = $item->product->mpn;
            $item->description_eng = $item->product->name;
            $item->variant_eng = array_key_first($item->product->data->variants) ?? '';
            $item->image = $item->product->productImage;
            $item->scm_code = implode("\n", $scmCodes);
        });

        return $purchaseGroup;
    }
}
