<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecentlyPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchases.*.rows.*.unit_price' => 'required|numeric|min:0|max:9999999999',
            'purchases.*.rows.*.delivery_days' => 'required|integer|max:65535',
        ];
    }

    public function attributes(): array
    {
        return [
            'purchases.*.rows.*.unit_price' => 'Unit price',
            'purchases.*.rows.*.delivery_days' => 'Delivery days',
        ];
    }
}
