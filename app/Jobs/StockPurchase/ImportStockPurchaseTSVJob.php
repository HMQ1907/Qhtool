<?php

namespace App\Jobs\StockPurchase;

use App\Actions\Sequences\GetSequenceAction;
use App\Imports\StockPurchase\StockPurchaseImport;
use App\Models\StockPurchaseRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Excel;

class ImportStockPurchaseTSVJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $userActivityId, protected string $path, protected int $userId = 0)
    {
        $this->onQueue('import');
    }

    /**
     * Execute the job.
     */
    public function handle(GetSequenceAction $getSequenceAction): void
    {
        $stockPurchaseRequest = StockPurchaseRequest::create([
            'key' => $getSequenceAction->handle('STOCK_PURCHASE')
        ]);
        
        (new StockPurchaseImport($stockPurchaseRequest->id, $this->userActivityId, $this->userId))
            ->import($this->path, 'local', Excel::TSV);
    }
}
