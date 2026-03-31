<?php

namespace App\Jobs\Quotation;

use App\Exports\Quotations\HistoryQuotationExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportHistoryQuotationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $input;
    protected string $filePath;

    public function __construct(array $input, string $filePath)
    {
        $this->input = $input;
        $this->filePath = $filePath;
        $this->onQueue('export');
    }

    public function handle(): void
    {
        ini_set('memory_limit', '512M');
        
        Excel::store(
            new HistoryQuotationExport($this->input),
            $this->filePath,
            'local',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
