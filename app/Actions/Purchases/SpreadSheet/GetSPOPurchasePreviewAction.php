<?php

namespace App\Actions\Purchases\SpreadSheet;

use App\Models\PurchaseTemplate;

class GetSPOPurchasePreviewAction
{
    public function handle(int $purchaseTemplateId): object
    {
        $purchaseTemplate = PurchaseTemplate::findOrFail($purchaseTemplateId);

        return (object) [
            'id' => 0,
            'code' => 'PREVIEW-' . $purchaseTemplate->name,
            'supplier_code' => 'PREVIEW-SUP',
            'purchase_date' => now(),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'supplier' => (object) [
                'code' => 'PREVIEW-SUP',
                'purchaseTemplate' => $purchaseTemplate,
            ],
            'purchases' => collect([]),
        ];
    }
}
