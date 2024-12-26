<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\t_ref_report_activities;
use App\Models\t_student_report_activities;
use App\Models\t_student_reports;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentReportRepository implements StudentReportRepositoryInterface
{

    public function customGetStudentReports($date = null, $parent = null, $teacher = null)
    {
        $query = t_student_reports::with([
            'student' => function ($query) {
                $query->select('S_ID', 'STUDENT_NAME','STUDENT_PARENT_U_ID')
                    ->orderBy('S_ID');
            },
            'student.parent' => function ($query) {
                $query->select('U_ID','U_NAME as STUDENT_PARENT_NAME');
            },
            'teacher' => function ($query) {
                $query->select('U_ID', 'U_NAME as TEACHER_NAME');
            },
            'activities' => function ($query) {
            $query->select('SRA_ID','SR_ID','ACTIVITY_NAME')
                ->orderBy('SRA_ID');
             },
            'activities.refActivities' => function ($query) {
                $query->select('RRA_ID','SRA_ID', 'ACTIVITY_TYPE', 'ACTIVITY_NAME',)
                    ->orderBy('RRA_ID');
            },
        ]);
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));

            $query->whereMonth('SR_DATE', $month)
                ->whereYear('SR_DATE', $year);
        }
        if ($parent) {
            $query->whereHas('student', function ($q) use ($parent) {
                $q->where('STUDENT_PARENT_U_ID', $parent);
            });
        }
        if ($teacher) {
            $query->whereHas('teacher', function ($q) use ($teacher) {
                $q->where('U_ID', $teacher);
            });
        }
        $data = $query->orderBy('SR_ID')->get();
        $reportArray = $data->map(function ($item) {
            $item->formatted_date = $item->SR_DATE ? \Carbon\Carbon::parse($item->SR_DATE)->format('d-m-Y') : null;
            return $item;
        })->toArray();
        return Helper::arrayChangeKeyCaseRecursive($reportArray);
    }
    public function getStudentReports($date = null)
    {
        $query = t_student_reports::with([
            'student' => function ($query) {
                $query->select('S_ID', 'STUDENT_NAME','STUDENT_PARENT_U_ID');
            },
            'student.parent' => function ($query) {
                $query->select('U_ID','U_NAME as STUDENT_PARENT_NAME');
            },
            'teacher' => function ($query) {
                $query->select('U_ID', 'U_NAME as TEACHER_NAME');
            }
        ]);
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));

            $query->whereMonth('SR_DATE', $month)
            ->whereYear('SR_DATE', $year);
        }

        $data = $query->get();
        $reportArray = json_decode($data->toJson(), true);
        return Helper::arrayChangeKeyCaseRecursive($reportArray);
    }

    public function getStudentReportById($id)
    {
        $data = t_student_reports::with([
            'student' => function ($query) {
                $query->select('S_ID', 'STUDENT_NAME','STUDENT_PARENT_U_ID');
            },
            'student.parent' => function ($query) {
                $query->select('U_ID','U_NAME as STUDENT_PARENT_NAME');
            },
            'teacher' => function ($query) {
                $query->select('U_ID', 'U_NAME as TEACHER_NAME');
            }
            ])
            ->where("SR_ID", $id)
            ->first();
        $reportArray = json_decode($data->toJson(), true);
        return Helper::arrayChangeKeyCaseRecursive($reportArray);
    }
    public function getStudentReportByParentId($id, $date = null)
    {
        $query = t_student_reports::with([
            'student' => function ($query) use ($id){
                $query->where('STUDENT_PARENT_U_ID', $id)->select('S_ID', 'STUDENT_NAME','STUDENT_PARENT_U_ID');
            },
            'student.parent' => function ($query) {
                $query->select('U_ID','U_NAME as STUDENT_PARENT_NAME');
            },
            'teacher' => function ($query) {
                $query->select('U_ID', 'U_NAME as TEACHER_NAME');
            }
        ]);
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));

            $query->whereMonth('SR_DATE', $month)
            ->whereYear('SR_DATE', $year);
        }

        // Execute the query
        $data = $query->get();

        // Convert to array and change key case
        $reportArray = json_decode($data->toJson(), true);
        return Helper::arrayChangeKeyCaseRecursive($reportArray);
    }
    public function createStudentReport($data)
    {
        $insertData = [
            'S_ID' => $data['S_ID'],
            'U_ID' => $data['U_ID'],
            'SR_CONTENT' => $data['SR_CONTENT'],
            'SR_DATE' => $data['SR_DATE'],
            'SR_IS_READ' => 'N',
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_AT' => now(),
        ];
        return t_student_reports::create($insertData);
    }

    public function updateStudentReport($data, $id)
    {
        $updateData = [
            'SYS_UPDATE_USER' => $data['SYS_UPDATE_USER'] ?? 'System',
            'SYS_UPDATE_AT' => now(),
        ];
        if (isset($data['SR_CONTENT'])) {
            $updateData['SR_CONTENT'] = $data['SR_CONTENT'];
        }
        if (isset($data['SR_DATE'])) {
            $updateData['SR_DATE'] = $data['SR_DATE'];
        }
        if (isset($data['SR_IS_READ'])) {
            $updateData['SR_IS_READ'] = $data['SR_IS_READ'];
        }
        return t_student_reports::where('SR_ID', $id)->update($updateData);
    }

    public function deleteStudentReport($id)
    {
        // TODO: Implement deleteStudentReport() method.
    }

    public function getAllStudentReportByStudentId($id, $date = null)
    {
        $query = t_student_reports::with([
            'student' => function ($query) {
                $query->select('S_ID', 'STUDENT_NAME', 'STUDENT_PARENT_U_ID');
            },
            'student.parent' => function ($query) {
                $query->select('U_ID', 'U_NAME as STUDENT_PARENT_NAME');
            },
            'teacher' => function ($query) {
                $query->select('U_ID', 'U_NAME as TEACHER_NAME');
            }
        ])->where('S_ID', $id);
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));

            $query->whereMonth('SR_DATE', $month)
            ->whereYear('SR_DATE', $year);
        }
        $data = $query->get();
        $reportArray = json_decode($data->toJson(), true);
        return Helper::arrayChangeKeyCaseRecursive($reportArray);
    }

    public function getAllStudentReportsByTeacherId($id, $date = null)
    {
        $query = t_student_reports::with([
            'student' => function ($query) {
                $query->select('S_ID', 'STUDENT_NAME', 'STUDENT_PARENT_U_ID');
            },
            'student.parent' => function ($query) {
                $query->select('U_ID', 'U_NAME as STUDENT_PARENT_NAME');
            },
            'teacher' => function ($query) {
                $query->select('U_ID', 'U_NAME as TEACHER_NAME');
            }
        ])->where('U_ID', $id);
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));

            $query->whereMonth('SR_DATE', $month)
            ->whereYear('SR_DATE', $year);
        }
        $data = $query->get();
        $reportArray = json_decode($data->toJson(), true);
        return Helper::arrayChangeKeyCaseRecursive($reportArray);
    }

    //activity
    public function getStudentReportsActivity()
    {
        return t_student_report_activities::all();
    }

    public function getStudentReportsActivityById($id)
    {
        return t_student_report_activities::where('SRA_ID', $id)->get();
    }

    public function getAllStudentReportActivityByReportId($id)
    {
        return t_student_report_activities::with('refActivities')->where('SR_ID', $id)
            ->orderBy('SRA_ID', 'asc')
            ->get();
    }

    public function createStudentReportActivity(array $data)
    {
        $insertData = [
            'SR_ID' => $data['SR_ID'],
            'ACTIVITY_NAME' => $data['ACTIVITY_NAME'],
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_AT' => now(),
        ];
        return t_student_report_activities::create($insertData);
    }

    public function updateStudentReportActivity(array $data, $id)
    {
        $updateData = [
            'SYS_UPDATE_USER' => $data['SYS_UPDATE_USER'] ?? 'System',
            'SYS_UPDATE_AT' => now(),
        ];
        if (isset($data['ACTIVITY_NAME'])) {
            $updateData['ACTIVITY_NAME'] = $data['ACTIVITY_NAME'];
        }
        return t_student_report_activities::where('SRA_ID', $id)->update($updateData);
    }

    public function deleteStudentReportActivity($id)
    {
        // TODO: Implement deleteStudentReportActivity() method.
    }

    public function getAllRefReportActivity()
    {
        return t_ref_report_activities::select([
            'RRA_ID',
            'SRA_ID',
            'ACTIVITY_TYPE',
            'ACTIVITY_NAME',
            'STATUS',
        ])->get();
    }

    public function getAllRefReportActivityById($id)
    {
        return t_ref_report_activities::select([
            'RRA_ID',
            'SRA_ID',
            'ACTIVITY_TYPE',
            'ACTIVITY_NAME',
            'STATUS',
        ])->where('SR_ID', $id)->get();
    }

    public function createRefReportActivity($data)
    {
        $insertData = [
            'SRA_ID' => $data['SRA_ID'],
            'ACTIVITY_TYPE' => $data['ACTIVITY_TYPE'],
            'ACTIVITY_NAME' => $data['ACTIVITY_NAME'],
            'STATUS' => $data['STATUS'] ?? 'BELUM MUNCUL',
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_AT' => now(),
        ];
        return t_ref_report_activities::create($insertData);
    }

    public function updateRefReportActivity($id, $data)
    {
        // TODO: Implement updateRefReportActivity() method.
    }

    public function deleteRefReportActivity()
    {
        // TODO: Implement deleteRefReportActivity() method.
    }
}
