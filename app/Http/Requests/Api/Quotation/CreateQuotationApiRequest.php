<?php

namespace App\Http\Requests\Api\Quotation;

use App\Rules\ValidSkuRule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateQuotationApiRequest extends FormRequest
{
    private const REQUIRED_STRING = 'required|string';
    private const NULLABLE_STRING = 'nullable|string';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'request_from' => self::REQUIRED_STRING,
            'reference_code' => self::REQUIRED_STRING,
            'supplier_key' => self::REQUIRED_STRING,

            'supplier.code' => self::NULLABLE_STRING,
            'supplier.unit_price' => self::NULLABLE_STRING,
            'supplier.currency' => self::NULLABLE_STRING,

            'product.qty' => 'required|integer',
            'product.sku' => ['required', 'string', new ValidSkuRule()],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ]));
    }
}
