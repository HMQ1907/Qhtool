<?php

namespace App\Http\Requests\Quotation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecentlyQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quotations.*.rows.*.unit_price' => 'required|numeric|min:0|max:9999999999',
            'quotations.*.rows.*.delivery_days' => 'required|integer|max:65535',
        ];
    }

    public function attributes(): array
    {
        return [
            'quotations.*.rows.*.unit_price' => 'Unit price',
            'quotations.*.rows.*.delivery_days' => 'Delivery days',
        ];
    }
}
