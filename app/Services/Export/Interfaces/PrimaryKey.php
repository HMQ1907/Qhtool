<?php

namespace App\Services\Export\Interfaces;

interface PrimaryKey
{
    /**
     * @return string
     */
    public function primaryKey(): string;
}
