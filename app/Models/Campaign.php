<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function videos()
    {
        return $this->hasMany(CampaignVideo::class);
    }
}
