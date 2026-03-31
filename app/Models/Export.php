<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Export extends BaseModel
{
    protected $table = 'exports';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'identify_codes' => 'array',
    ];

    use HasFactory;
}
