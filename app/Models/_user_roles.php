<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _user_roles extends Model
{
    protected $table = '_user_roles';
    protected $primaryKey = 'UR_ID';

    protected $fillable = [
        'ROLE_NAME',
    ];
    public $timestamps = false;
}
