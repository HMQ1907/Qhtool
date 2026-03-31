<?php

namespace App\Services\Export\Interfaces;

interface WithHeadings
{
    /**
     * @return array
     */
    public function headings(): array;
}
