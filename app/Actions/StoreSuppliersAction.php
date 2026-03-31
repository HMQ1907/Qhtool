<?php

namespace App\Actions;

use App\Models\Supplier;

class StoreSuppliersAction
{
    public function handle($suppliers): Supplier
    {
        $data = (object)[
            'key' => $suppliers['key'],
            'name' => $suppliers['name'],
            'channel' => $suppliers['channel'],
            'payment_method' => $suppliers['payment_method'],
            'currency' => $suppliers['currency'],
            'delivery_day_weight' => $suppliers['delivery_day_weight'],
        ];

        $hash = md5(json_encode($data));

        $supplier = Supplier::firstOrNew(['code' => $suppliers['key']]);

        $supplier->data = $data;
        $supplier->hash = $hash;
        $supplier->save();

        return $supplier;
    }
}
