<?php

/**
 * This file is part of RiverCrane's project.
 * (c) 2022 RiverCrane Corp.
 *
 * @copyright 2022 RiverCrane Corp.
 */

namespace App\Models;

use App\Models\Microzero\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ManagementToolHistory extends BaseModel
{
    protected $table = 'management_tool_history';

    protected $fillable = ['id', 'module', 'operation', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y/m/d H:i:s');
    }

    public function userActivityImportResult(): HasOne
    {
        return $this->hasOne(UserActivityImportResult::class, 'user_activity_id', 'id');
    }
}
