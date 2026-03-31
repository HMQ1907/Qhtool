<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityImportError extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_activity_import_errors';

    protected $fillable = [
        'user_activity_import_result_id',
        'identify_name',
        'identify_value',
        'error_message',
    ];
}
