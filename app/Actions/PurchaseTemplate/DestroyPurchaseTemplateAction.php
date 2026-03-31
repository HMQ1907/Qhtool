<?php

namespace App\Actions\PurchaseTemplate;

use App\Models\PurchaseTemplate;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\FileUploadHelper;
use Throwable;

class DestroyPurchaseTemplateAction
{
    public function handle(array $ids): array
    {
        try {
            $usedTemplateIds = Supplier::whereIn('purchase_template_id', $ids)->get()->pluck('purchase_template_id')->toArray();

            if (!empty($usedTemplateIds)) {
                $templateNames = PurchaseTemplate::whereIn('id', $usedTemplateIds)->get()->pluck('name')->toArray();

                return [
                    'success' => false,
                    'message' => [
                        'type' => 'error',
                        'title' => 'Error',
                        'messages' => 'Please remove the template from the supplier first: <span class="text-red-500">' . implode(',', $templateNames) . '</span> is in use.',
                    ]
                ];
            }

            DB::transaction(function () use ($ids) {
                foreach ($ids as $id) {
                    $template = PurchaseTemplate::findOrFail($id);

                    if ($template->webike_logo_url) {
                        FileUploadHelper::deleteOldFile($template->webike_logo_url);
                    }

                    if ($template->authorized_signature_url) {
                        FileUploadHelper::deleteOldFile($template->authorized_signature_url);
                    }

                    Supplier::where('purchase_template_id', $id)->update(['purchase_template_id' => null]);
                    $template->delete();
                }

                createManagementToolHistory('PurchaseTemplates', 'Delete PurchaseTemplate: ' . implode(',', $ids));
            }, NUMBER_TRANSACTION);

            return [
                'success' => true,
                'message' => [
                    'type' => 'success',
                    'title' => 'Success',
                    'messages' => 'Deleted purchase templates IDs:  ' . implode(',', $ids) . ' successfully!',
                ]
            ];
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'ids' => $ids,
            ]);

            throw $th;
        }
    }
}
