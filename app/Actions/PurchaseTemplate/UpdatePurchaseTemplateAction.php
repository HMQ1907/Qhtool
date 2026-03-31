<?php

namespace App\Actions\PurchaseTemplate;

use App\Helpers\FileUploadHelper;
use App\Models\PurchaseTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdatePurchaseTemplateAction
{
    public function handle(array $input, int $id)
    {
        try {
            return DB::transaction(function () use ($input, $id) {
                $template = PurchaseTemplate::findOrFail($id);
                if ($input['webike_logo_url'] != $template->webike_logo_url) {
                    
                    $input['webike_logo_url'] = $this->handleFileUpload(
                        $input['webike_logo_url'] ?? null,
                        'webike_logo',
                        $template->webike_logo_url
                    );
                }

                if ($input['authorized_signature_url'] != $template->authorized_signature_url) {
                    $input['authorized_signature_url'] = $this->handleFileUpload(
                        $input['authorized_signature_url'] ?? null,
                        'authorized_signature',
                        $template->authorized_signature_url
                    );
                }

                $template->fill($input);
                $template->updated_by = Auth::id() ?? 0;
                $template->save();
                createManagementToolHistory('PurchaseTemplates', 'Update PurchaseTemplate ID: ' . $template->id);

                return true;
            }, NUMBER_TRANSACTION);
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'input' => $input,
                'id' => $id,
                'error' => $th->getMessage()
            ]);

            throw $th;
        }
    }

    protected function handleFileUpload($file, string $prefix, ?string $oldFilePath = null): ?string
    {
        if (!$file) {
            return null;
        }

        if ($oldFilePath) {
            FileUploadHelper::deleteOldFile($oldFilePath);
        }

        return FileUploadHelper::handleFileUpload($file, $prefix);
    }
}
