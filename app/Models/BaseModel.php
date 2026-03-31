<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function getDatabaseName()
    {
        return with(new static)->getConnection()->getDatabaseName();
    }
}
