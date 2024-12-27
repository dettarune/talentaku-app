<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class t_student_reports extends Model
{
    protected $table = 't_student_reports';

    protected $primaryKey = 'SR_ID';
    public $timestamps = false;

    protected $fillable = [
        'S_ID',
        'U_ID',
        'SR_TITLE',
        'SR_CONTENT',
        'SR_DATE',
        'SR_IS_READ',
    ];
    protected $casts = [
        'SR_DATE' => 'datetime',
    ];

    /**
     * Define a relationship to the `t_students` table.
     */
    public function student()
    {
        return $this->belongsTo(t_students::class, 'S_ID', 'S_ID');
    }

    /**
     * Define a relationship to the `users` table (teachers).
     */
    public function teacher()
    {
        return $this->belongsTo(Users::class, 'U_ID', 'U_ID');
    }

    /**
     * Define a relationship to the `t_student_report_activities` table.
     */
    public function activities()
    {
        return $this->hasMany(t_student_report_activities::class, 'SR_ID', 'SR_ID');
    }
}
