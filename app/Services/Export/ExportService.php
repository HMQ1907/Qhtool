<?php

namespace App\Services\Export;

use App\Services\BaseService;
use App\Services\Export\Interfaces\FromArray;
use App\Services\Export\Interfaces\FromQuery;
use App\Services\Export\Interfaces\PrimaryKey;
use App\Services\Export\Interfaces\WithChunkReading;
use App\Services\Export\Interfaces\WithCustomCsvSettings;
use App\Services\Export\Interfaces\WithHeadings;
use App\Services\Export\Interfaces\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ExportService extends BaseService
{
    protected object $exportModel;
    protected string $location;
    protected string $format;
    protected array $interfaces;
    protected int $chunkSize = 1000;
    protected array $headings = [];
    protected string $primaryKey = 'id';
    protected $myFile;
    protected array $settings = [
        'delimiter' => ",",
    ];

    public function __construct(object $exportModel, string $location, string $format)
    {
        $this->exportModel = $exportModel;
        $this->location = $location;
        $this->format = $format;
        $this->interfaces = class_implements($exportModel);

        if (in_array(WithChunkReading::class, $this->interfaces)) {
            $this->chunkSize = $this->exportModel->chunkSize();
        }

        if (in_array(WithHeadings::class, $this->interfaces)) {
            $this->headings = $this->exportModel->headings();
        }

        if (in_array(PrimaryKey::class, $this->interfaces)) {
            $this->primaryKey = $this->exportModel->primaryKey();
        }

        if (in_array(WithCustomCsvSettings::class, $this->interfaces)) {
            $customSettings = $this->exportModel->getCsvSettings();

            foreach ($customSettings as $key => $customSetting) {
                $this->settings[$key] = $customSetting;
            }
        }
    }

    public function handle(): void
    {
        $directory = dirname($this->location);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $this->myFile = fopen($this->location . '.tmp', 'a');

        if (!empty($this->headings)) {
            $header = implode($this->settings['delimiter'], $this->headings) . "\n";
            fwrite($this->myFile, $header);
        }

        if (in_array(FromQuery::class, $this->interfaces)) {
            $this->exportFromQuery();
        } elseif (in_array(FromArray::class, $this->interfaces)) {
            $this->exportFromArray();
        }

        fclose($this->myFile);
        rename($this->location . '.tmp', $this->location);
    }

    protected function exportFromQuery(): void
    {
        $this->exportModel->query()->chunkById($this->chunkSize, function (Collection $collection) {
            foreach ($collection as $item) {
                fwrite($this->myFile, $this->getOutput($item));
            }
        }, $this->primaryKey, 'id');
    }

    protected function exportFromArray(): void
    {
        $chunksArr = array_chunk($this->exportModel->array(), $this->chunkSize);

        foreach ($chunksArr as $chunksArr => $chunks) {
            foreach ($chunks as $item) {
                fwrite($this->myFile, $this->getOutput($item));
            }
        }
    }

    protected function getOutput(Model|array $item): string
    {
        if (in_array(WithMapping::class, $this->interfaces)) {
            return implode($this->settings['delimiter'], $this->exportModel->map($item)) . "\n";
        }

        if (!is_array($item)) {
            $item = $item->toArray();
        }

        return implode($this->settings['delimiter'], $item) . "\n";
    }
}
