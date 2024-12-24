<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class t_classrooms extends Model
{
    protected $table = 't_classrooms';
    protected $primaryKey = 'CLSRM_ID';

    protected $fillable = [
        'CLSRM_NAME',
        'CLSRM_TYPE',
        'CLSRM_GRADE',
        'CLSRM_DESCRIPTION',
        'SYS_CREATE_USER',
        'SYS_UPDATE_USER',
    ];
    public $timestamps = false;
}
