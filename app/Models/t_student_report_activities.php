<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class t_student_report_activities extends Model
{
    protected $table = 't_student_report_activities';

    protected $primaryKey = 'SRA_ID';
    public $timestamps = false;
    protected $fillable = [
        'SR_ID',
        'ACTIVITY_TYPE',
        'ACTIVITY_NAME',
        'STATUS',
        'SYS_CREATED_USER',
        'SYS_UPDATED_USER',
    ];
    protected $casts = [
        'SYS_CREATE_AT' => 'datetime',
        'SYS_UPDATE_AT' => 'datetime',
    ];

    /**
     * Define a relationship to the `t_student_reports` table.
     */
    public function studentReport()
    {
        return $this->belongsTo(t_student_reports::class, 'SR_ID', 'SR_ID');
    }

    public function refActivities()
    {
        return $this->hasMany(t_ref_report_activities::class, 'SRA_ID', 'SRA_ID');
    }
}
