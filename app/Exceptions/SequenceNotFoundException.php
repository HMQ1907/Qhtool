<?php

namespace App\Exceptions;

use Exception;

class SequenceNotFoundException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct("Sequence with key {$key} not found");
    }
}
