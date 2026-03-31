<?php

namespace App\Imports;

use App\Models\ManagementToolHistory;
use App\Models\UserActivity;
use App\Models\UserActivityImportResult;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;

abstract class BaseImport implements OnEachRow, WithChunkReading, WithCustomCsvSettings, WithStartRow, WithEvents
{
    use Importable;

    protected int $totalRecords = 0;
    protected int $totalSuccess = 0;
    protected int $totalFailed = 0;
    protected int $totalUnchanged = 0;
    protected array $errorRecords = [];
    protected ManagementToolHistory $userActivity;
    protected UserActivityImportResult $userActivityImportResult;
    protected int $userId;

    public function __construct(
        protected int $userActivityId,
        int $userId = 0
    ) {
        $this->totalRecords = 0;
        $this->totalSuccess = 0;
        $this->totalFailed = 0;
        $this->totalUnchanged = 0;
        $this->errorRecords = [];
        $this->userActivity = ManagementToolHistory::findOrFail($this->userActivityId);
        $this->userActivityImportResult = $this->userActivity->userActivityImportResult()->create([
            'user_activity_id' => $this->userActivity->id,
        ]);
        $this->userId = $userId;
    }

    abstract public function onRow(Row $row);

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "\t"
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                $this->userActivity->update([
                    'operation' => str_replace('Waiting', 'Starting', $this->userActivity->operation),
                ]);
                $this->userActivity->refresh();
            },
            AfterImport::class => function () {
                $this->userActivity->update([
                    'operation' => str_replace('Starting', 'Finish', $this->userActivity->operation),
                ]);

                $this->userActivityImportResult->update([
                    'total_records' => $this->totalRecords,
                    'total_success' => $this->totalSuccess,
                    'total_failed' => $this->totalFailed,
                    'total_unchanged' => $this->totalUnchanged,
                ]);

                if (count($this->errorRecords) > 0) {
                    $this->userActivityImportResult->userActivityImportErrors()->insert($this->errorRecords);
                }
            },
        ];
    }
}
