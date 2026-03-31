<?php

namespace App\Services\Export\Interfaces;

interface WithMapping
{
    /**
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array;
}
