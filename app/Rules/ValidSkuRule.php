<?php

namespace App\Rules;

use App\Actions\Webike\GetOrderProductsAction;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Throwable;

class ValidSkuRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $action = app()->make(GetOrderProductsAction::class);
            $product = $action->handle($value);
            if (is_null($product)) {
                $fail('Sku is not exist.');
            }
        } catch (Throwable) {
            $fail('SKU validation failed.');
        }
    }
}
