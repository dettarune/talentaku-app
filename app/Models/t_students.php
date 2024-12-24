<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class t_students extends Model
{
    protected $table = 't_students';
    protected $primaryKey = 'S_ID';

    protected $fillable = [
        'STUDENT_NAME',
        'STUDENT_ROLL_NUMBER',
        'STUDENT_PARENT_U_ID',
        'STUDENT_SEX',
        'CLSRM_ID',
        'STUDENT_IMAGE_PROFILE',
    ];
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(Users::class, 'STUDENT_PARENT_U_ID');
    }

    public function classroom()
    {
        return $this->belongsTo(t_classrooms::class, 'CLSRM_ID');
    }

    public function reports()
    {
        return $this->hasMany(t_student_reports::class, 'S_ID');
    }
}
