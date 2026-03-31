<?php

namespace App\Services\Export\Interfaces;

interface WithCustomCsvSettings
{
    /**
     * @return array
     */
    public function getCsvSettings(): array;
}
