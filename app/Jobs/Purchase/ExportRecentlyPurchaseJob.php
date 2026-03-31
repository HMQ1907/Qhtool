<?php

namespace App\Jobs\Purchase;

use App\Exports\Purchases\RecentlyPurchaseExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportRecentlyPurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;
    protected string $filePath;

    public function __construct(array $data, string $filePath)
    {
        $this->data = $data;
        $this->filePath = $filePath;
        $this->onQueue('export');
    }

    public function handle(): void
    {
        Excel::store(
            new RecentlyPurchaseExport($this->data),
            $this->filePath,
            'local',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
