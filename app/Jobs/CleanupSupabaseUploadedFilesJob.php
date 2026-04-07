<?php

namespace App\Jobs;

use App\Services\SupabaseStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CleanupSupabaseUploadedFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 60;

    /** @param array<int, string> $objectPaths */
    public function __construct(
        private readonly array $objectPaths,
    ) {}

    public function handle(SupabaseStorageService $supabaseStorage): void
    {
        foreach (array_reverse($this->objectPaths) as $objectPath) {
            try {
                $supabaseStorage->deleteObject($objectPath);
            } catch (Throwable $exception) {
                Log::warning('[CleanupSupabaseUploadedFilesJob] Không thể xóa file tạm trên Supabase', [
                    'path' => $objectPath,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}