<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class t_ref_report_activities extends Model
{
    use HasFactory;

    protected $table = 't_ref_report_activities';
    protected $primaryKey = 'RRA_ID';
    public $timestamps = false;
    protected $fillable = [
        'SRA_ID',
        'ACTIVITY_TYPE',
        'ACTIVITY_NAME',
        'STATUS',
        'SYS_CREATE_USER',
        'SYS_UPDATE_USER',
    ];
    protected $dates = [
        'SYS_CREATE_AT',
        'SYS_UPDATE_AT',
    ];

    public function studentReportActivity()
    {
        return $this->belongsTo(t_student_report_activities::class, 'SRA_ID');
    }
}
