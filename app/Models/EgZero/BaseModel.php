<?php

namespace App\Models\EgZero;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'eg_zero';
}
