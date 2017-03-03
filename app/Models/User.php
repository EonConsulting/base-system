<?php

namespace App\Models;

use EONConsulting\RolesPermissions\Traits\HasPermissionTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasPermissionTrait;

    protected $fillable = [
        'name', 'email', 'password', 'last_department_id'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

}
