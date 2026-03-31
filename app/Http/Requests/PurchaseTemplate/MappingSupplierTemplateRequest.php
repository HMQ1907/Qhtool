<?php
// cspell:ignore webike
namespace App\Http\Requests\PurchaseTemplate;

use Illuminate\Foundation\Http\FormRequest;

class MappingSupplierTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_template_id' => 'required|integer|exists:purchase_templates,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'purchase_template_id' => 'Purchase Template ID',
        ];
    }
}
