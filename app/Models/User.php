<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'free_images_left',
        'free_videos_left',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
