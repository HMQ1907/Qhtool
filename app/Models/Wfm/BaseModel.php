<?php

namespace App\Models\Wfm;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'wfm';
}
