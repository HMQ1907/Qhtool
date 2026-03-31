<?php

namespace App\Actions\Quotations;

use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreRecentlyQuotationAction
{
    public function handle(array $input): bool
    {
        try {
            if (empty($input)) {
                return false;
            }

            DB::transaction(function () use ($input) {
                foreach ($input['quotations'][0]['rows'] as $quotationData) {
                    $quotation = Quotation::where('id', $quotationData['id'])->first();

                    if ($quotation) {
                        $quotation->checked = Quotation::CHECKED;
                        $quotation->unit_price = $quotationData['unit_price'];
                        $quotation->delivery_days = $quotationData['delivery_days'];
                        $quotation->save();
                    }
                }

                $listIdentifyCode = array_column($input['quotations'][0]['rows'], 'identify_code');
                createManagementToolHistory('Quotations', 'Update quotations: ' . implode(',', $listIdentifyCode));
            }, NUMBER_TRANSACTION);

            return true;
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
            ]);

            throw $th;
        }
    }
}
