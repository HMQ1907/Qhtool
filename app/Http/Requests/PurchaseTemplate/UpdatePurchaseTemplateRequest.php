<?php
// cspell:ignore webike
namespace App\Http\Requests\PurchaseTemplate;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'memo' => 'nullable|string|max:255',
            'webike_logo_url' => 'nullable',
            'webike_name' => 'nullable|string|max:255',
            'webike_street_address' => 'nullable|string|max:255',
            'webike_district_sub_district' => 'nullable|string|max:255',
            'webike_city_municipality' => 'nullable|string|max:255',
            'webike_state_province' => 'nullable|string|max:255',
            'webike_postal_code_zip_code' => 'nullable|string|max:255',
            'webike_country' => 'nullable|string|max:255',
            'webike_phone_number' => 'nullable|string|',
            'ship_to_name' => 'nullable|string|max:255',
            'ship_to_street_address' => 'nullable|string|max:255',
            'ship_to_district_sub_district' => 'nullable|string|max:255',
            'ship_to_city_municipality' => 'nullable|string|max:255',
            'ship_to_state_province' => 'nullable|string|max:255',
            'ship_to_postal_code_zip_code' => 'nullable|string|max:255',
            'ship_to_country' => 'nullable|string|max:255',
            'ship_to_phone_number' => 'nullable|string|',
            'items' => 'required|array|min:5',
            'is_display_total_amount' => 'nullable|boolean',
            'is_display_webike_address' => 'nullable|boolean',
            'note' => 'nullable|string|max:255',
            'authorized_signature_url' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'memo' => 'Memo',
            'webike_logo_url' => 'Webike Logo URL',
            'webike_name' => 'Webike Name',
            'webike_street_address' => 'Webike Street Address',
            'webike_district_sub_district' => 'Webike District Sub District',
            'webike_city_municipality' => 'Webike City Municipality',
            'webike_state_province' => 'Webike State Province',
            'webike_postal_code_zip_code' => 'Webike Postal Code ZIP Code',
            'webike_country' => 'Webike Country',
            'webike_phone_number' => 'Webike Phone Number',
            'ship_to_name' => 'Ship To Name',
            'ship_to_street_address' => 'Ship To Street Address',
            'ship_to_district_sub_district' => 'Ship To District Sub District',
            'ship_to_city_municipality' => 'Ship To City Municipality',
            'ship_to_state_province' => 'Ship To State Province',
            'ship_to_postal_code_zip_code' => 'Ship To Postal Code ZIP Code',
            'ship_to_country' => 'Ship To Country',
            'ship_to_phone_number' => 'Ship To Phone Number',
            'items' => 'Items',
            'is_display_total_amount' => 'Is Display Total Amount',
            'is_display_webike_address' => 'Is Display Webike Address',
            'note' => 'Note',
            'authorized_signature_url' => 'Authorized Signature URL',
        ];
    }
}
