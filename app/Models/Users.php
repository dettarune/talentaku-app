<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    protected $table = '_users';
    protected $primaryKey = 'U_ID';

    protected $fillable = [
        'U_NAME',
        'U_PASSWORD_HASH',
        'UR_ID',
        'U_SEX',
        'U_EMAIL',
        'U_ADDRESS',
        'U_IMAGE_PROFILE',
        'U_LOGIN_TOKEN',
        'U_LOGIN_TIME',
        'U_LOGIN_EXPIRED_TIME',
        'U_LOGOUT_TIME',
        'SYS_CREATE_TIME',
        'SYS_CREATE_USER',
        'SYS_UPDATE_TIME',
        'SYS_UPDATE_USER',
    ];
    public $timestamps = false;

    protected $hidden = [
        'U_PASSWORD_HASH',
        'U_LOGIN_TOKEN',
    ];

    public function role()
    {
        return $this->belongsTo(_user_roles::class, 'UR_ID');
    }
}
