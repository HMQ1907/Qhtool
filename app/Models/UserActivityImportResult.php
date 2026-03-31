<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserActivityImportResult extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_activity_import_results';

    protected $fillable = [
        'user_activity_id',
        'total_records',
        'total_success',
        'total_unchanged',
        'total_failed',
    ];

    public function userActivityImportErrors(): HasMany
    {
        return $this->hasMany(UserActivityImportError::class, 'user_activity_import_result_id', 'id');
    }
}
