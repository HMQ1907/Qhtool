<?php

namespace App\Actions\PurchaseTemplate;

use App\Helpers\FileUploadHelper;
use App\Models\PurchaseTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class StorePurchaseTemplateAction
{
    public function handle(array $input)
    {
        try {
            DB::transaction(function () use ($input) {
                $webikeLogoUrl = FileUploadHelper::handleFileUpload($input['webike_logo_url'] ?? null, 'webike_logo');
                $authorizedSignatureUrl = FileUploadHelper::handleFileUpload($input['authorized_signature_url'] ?? null, 'authorized_signature');

                unset($input['webike_logo_url']);
                unset($input['authorized_signature_url']);

                $template = new PurchaseTemplate();

                $template->fill($input);
                $template->created_by = Auth::id() ?? 0;
                $template->updated_by = Auth::id() ?? 0;

                if ($webikeLogoUrl !== null) {
                    $template->webike_logo_url = $webikeLogoUrl;
                }

                if ($authorizedSignatureUrl !== null) {
                    $template->authorized_signature_url = $authorizedSignatureUrl;
                }

                $template->save();

                createManagementToolHistory('PurchaseTemplates', 'Create PurchaseTemplate ID: ' . $template->id);
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
