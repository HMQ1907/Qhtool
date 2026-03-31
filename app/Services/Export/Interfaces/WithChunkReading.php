<?php

namespace App\Services\Export\Interfaces;

interface WithChunkReading
{
    /**
     * @return int
     */
    public function chunkSize(): int;
}
