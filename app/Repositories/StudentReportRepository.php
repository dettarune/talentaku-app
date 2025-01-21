<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\t_ref_report_activities;
use App\Models\t_student_report_activities;
use App\Models\t_student_reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentReportRepository implements StudentReportRepositoryInterface
{

    public function customGetStudentReports($date = null, $parent = null, $teacher = null, $studentId = null)
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
            },
            'activities' => function ($query) {
                $query->select('SRA_ID', 'SR_ID', 'ACTIVITY_NAME')
                    ->orderBy('SRA_ID');
            },
            'activities.refActivities' => function ($query) {
                $query->select('RRA_ID', 'SRA_ID', 'ACTIVITY_TYPE', 'ACTIVITY_NAME', 'STATUS')
                    ->orderBy('RRA_ID');
            },
        ]);

        if ($date) {
            // Check if date is in format Y-m or Y-m-d
            if (strlen($date) === 7) {
                // Format Y-m
                $month = date('m', strtotime($date));
                $year = date('Y', strtotime($date));
                $query->whereMonth('SR_DATE', $month)
                    ->whereYear('SR_DATE', $year);
            } elseif (strlen($date) === 10) {
                // Format Y-m-d
                $query->whereDate('SR_DATE', $date);
            }
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
        if ($studentId) {
            $query->where('S_ID', $studentId);
        }

        $data = $query->orderBy('SR_ID')->get();

        // Format data output
        $formattedData = $data->map(function ($report) {
            return [
                'SR_ID' => $report->SR_ID,
                'S_ID' => $report->S_ID,
                'U_ID' => $report->U_ID,
                'SR_TITLE' => $report->SR_TITLE,
                'SR_CONTENT' => $report->SR_CONTENT,
                'SR_DATE' => $report->SR_DATE,
                'FORMATTED_DATE' => $report->SR_DATE ? \Carbon\Carbon::parse($report->SR_DATE)->format('d-F-Y') : null,
                'STUDENT' => [
                    'S_ID' => $report->student->S_ID,
                    'STUDENT_NAME' => $report->student->STUDENT_NAME,
                    'STUDENT_PARENT_U_ID' => $report->student->STUDENT_PARENT_U_ID,
                    'PARENT' => [
                        'U_ID' => $report->student->parent->U_ID,
                        'STUDENT_PARENT_NAME' => $report->student->parent->STUDENT_PARENT_NAME,
                    ],
                ],
                'TEACHER' => [
                    'U_ID' => $report->teacher->U_ID,
                    'TEACHER_NAME' => $report->teacher->TEACHER_NAME,
                ],
                'ACTIVITIES' => $report->activities->map(function ($activity) {
                    return [
//                        'SRA_ID' => $activity->SRA_ID,
//                        'SR_ID' => $activity->SR_ID,
                        'ACTIVITY_NAME' => $activity->ACTIVITY_NAME,
                        'REF_ACTIVITIES' => $activity->refActivities->map(function ($refActivity) {
                            return [
//                                'RRA_ID' => $refActivity->RRA_ID,
//                                'SRA_ID' => $refActivity->SRA_ID,
                                'ACTIVITY_TYPE' => $refActivity->ACTIVITY_TYPE == 'Undefined'?null:$refActivity->ACTIVITY_TYPE,
                                'ACTIVITY_NAME' => $refActivity->ACTIVITY_NAME,
                                'STATUS' => $refActivity->STATUS,
                            ];
                        }),
                    ];
                }),
            ];
        });
        return $formattedData;
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
        $report = t_student_reports::with([
            'student' => function ($query) {
                $query->select('S_ID', 'STUDENT_NAME', 'STUDENT_PARENT_U_ID')->withTrashed();
            },
            'student.parent' => function ($query) {
                $query->select('U_ID', 'U_NAME as STUDENT_PARENT_NAME');
            },
            'teacher' => function ($query) {
                $query->select('U_ID', 'U_NAME as TEACHER_NAME');
            },
            'activities' => function ($query) {
                $query->select('SRA_ID', 'SR_ID', 'ACTIVITY_NAME')
                    ->orderBy('SRA_ID');
            },
            'activities.refActivities' => function ($query) {
                $query->select('RRA_ID', 'SRA_ID', 'ACTIVITY_TYPE', 'ACTIVITY_NAME', 'STATUS')
                    ->orderBy('RRA_ID');
            },
        ])->find($id);


        $formattedReport = [
            'SR_ID' => $report->SR_ID,
            'S_ID' => $report->S_ID,
            'U_ID' => $report->U_ID,
            'SR_TITLE' => $report->SR_TITLE,
            'SR_CONTENT' => $report->SR_CONTENT,
            'SR_DATE' => $report->SR_DATE,
            'FORMATTED_DATE' => $report->SR_DATE ? \Carbon\Carbon::parse($report->SR_DATE)->format('d-F-Y') : null,
            'STUDENT' => [
                'S_ID' => $report->student->S_ID,
                'STUDENT_NAME' => $report->student->STUDENT_NAME,
                'STUDENT_PARENT_U_ID' => $report->student->STUDENT_PARENT_U_ID,
                'PARENT' => [
                    'U_ID' => $report->student->parent->U_ID,
                    'STUDENT_PARENT_NAME' => $report->student->parent->STUDENT_PARENT_NAME,
                ],
            ],
            'TEACHER' => [
                'U_ID' => $report->teacher->U_ID,
                'TEACHER_NAME' => $report->teacher->TEACHER_NAME,
            ],
            'ACTIVITIES' => $report->activities->map(function ($activity) {
                return [
                    'ACTIVITY_NAME' => $activity->ACTIVITY_NAME,
                    'REF_ACTIVITIES' => $activity->refActivities->map(function ($refActivity) {
                        return [
                            'ACTIVITY_TYPE' => $refActivity->ACTIVITY_TYPE == 'Undefined'?null:$refActivity->ACTIVITY_TYPE,
                            'ACTIVITY_NAME' => $refActivity->ACTIVITY_NAME,
                            'STATUS' => $refActivity->STATUS,
                        ];
                    }),
                ];
            }),
        ];

        return $formattedReport;
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
            'SR_TITLE' => $data['SR_TITLE'],
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

    public function getDatatables($S_ID, $date = null)
    {
        {
            $columns = array(
                0 => 'SR_TITLE',
                1 => 'SR_CONTENT',
                2 => 'SR_DATE',
                3 => 'TEACHER_NAME',
            );

            $limit = $_POST['length'];
            $start = $_POST['start'];
            $orderColumnIndex = $_POST['order']['0']['column'] ?? null;
            if (isset($columns[$orderColumnIndex])) {
                $order = $columns[$orderColumnIndex];
            } else {
                $order = 'SR_TITLE';
            }

            $dir = $_POST['order']['0']['dir'] ?? 'asc';
//        $dir = $_POST['order']['0']['dir'];

            $baseData = t_student_reports::with([
                'student' => function ($query) {
                    $query->select('S_ID', 'STUDENT_NAME', 'STUDENT_PARENT_U_ID')->withTrashed();
                },
                'student.parent' => function ($query) {
                    $query->select('U_ID', 'U_NAME as STUDENT_PARENT_NAME');
                },
                'teacher' => function ($query) {
                    $query->select('U_ID', 'U_NAME as TEACHER_NAME');
                },
                'activities' => function ($query) {
                    $query->select('SRA_ID', 'SR_ID', 'ACTIVITY_NAME')
                        ->orderBy('SRA_ID');
                },
                'activities.refActivities' => function ($query) {
                    $query->select('RRA_ID', 'SRA_ID', 'ACTIVITY_TYPE', 'ACTIVITY_NAME', 'STATUS')
                        ->orderBy('RRA_ID');
                },
            ]);
            $baseData->join('_users as teachers', 't_student_reports.U_ID', '=', 'teachers.U_ID')
                ->select('t_student_reports.*', 'teachers.U_NAME as TEACHER_NAME');
            if ($date) {
                // Check if date is in format Y-m or Y-m-d
                if (strlen($date) === 7) {
                    // Format Y-m
                    $month = date('m', strtotime($date));
                    $year = date('Y', strtotime($date));
                    $baseData->whereMonth('SR_DATE', $month)
                        ->whereYear('SR_DATE', $year);
                } elseif (strlen($date) === 10) {
                    // Format Y-m-d
                    $baseData->whereDate('SR_DATE', $date);
                }
            }
            $baseData->where('S_ID', '=',$S_ID);
            $baseCount = $baseData;
            $totalData = $baseCount->count();
            $totalFiltered = $totalData;
            if (empty($_POST['search']['value'])) {
                $baseData->where('S_ID', '=',$S_ID);
                $baseData->orderBy($order, $dir);
                $baseData->limit($limit);
                $baseData->offset($start);
                $dtData = $baseData->get();
            } else {
                $search = $_POST['search']['value'];
                $baseData->where('S_ID', '=',$S_ID);

                $baseData->where(function ($query) use ($search) {
                    $query->where("t_student_reports.SR_TITLE", "like", "%" . $search . "%")
                        ->orWhere("t_student_reports.SR_CONTENT", "like", "%" . $search . "%")
                        ->orWhere("t_student_reports.SR_DATE", "like", "%" . $search . "%");
                });
                $filterCount = $baseData;

                $baseData->orderBy($order, $dir);
                $baseData->limit($limit);
                $baseData->offset($start);
                $dtData = $baseData->get();

                $totalFiltered = $filterCount->count();
                if (!($totalFiltered)) $totalFiltered = 0;
            }

            foreach ($dtData as $key => $value) {
                $nestedData["SR_TITLE"] = "<span style='opacity: 0.8'>" . $value->SR_TITLE . "</span>";
                $nestedData["SR_CONTENT"] = "<span style='opacity: 0.8'>" . Str::limit($value->SR_CONTENT, 100) . "</span>";
                $nestedData["TEACHER_NAME"] = "<span style='opacity: 0.8'>" . $value->teacher->TEACHER_NAME . "</span>";
                $nestedData["FORMATTED_DATE"] = "<span style='opacity: 0.8'>" . \Carbon\Carbon::parse($value->SR_DATE)->format('d F Y') . "</span>";

                $action = "";
                $action .= '
        <script type="text/javascript">
             var rowData_' . md5($value->{"SR_ID"}) . ' = {
                "SR_ID" : "' . $value->{"SR_ID"} . '",
                "SR_TITLE" : "' . $value->{"SR_TITLE"} . '",
                "SR_CONTENT" : "' . $value->{"STUDENT_ROLL_NUMBER"} . '",
                "STUDENT_PARENT" : "' . $value->{"STUDENT_PARENT"} . '",
                "STUDENT_SEX" : "' . $value->{"STUDENT_SEX"} . '",
                "CLASSROOM_NAME" : "' . $value->{"CLASSROOM_NAME"} . '",
                "CLSRM_ID" : "' . $value->{"CLSRM_ID"} . '",
                "STUDENT_PARENT_U_ID" : "' . $value->{"STUDENT_PARENT_U_ID"} . '",
            };
        </script>
    ';
                $action .= '<div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                            <a href="javascript:detailStudentReport(rowData_' . md5($value->{"SR_ID"}) . ')" class="dropdown-item">
                            Detail Student Report
                            </a>
                        </li>
                        <li>
            <a href="javascript:void(0);" onclick="deleteReport(' . $value->{"SR_ID"} . ')" class="dropdown-item">
                Delete
            </a>
        </li>

                    </ul>
                </div>';


                $nestedData["Action"] = $action;
                $data[] = $nestedData;
            }

            $arrData = array(
                "draw" => intval($_POST['draw']),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => isset($data) ? $data : []
            );

            return json_encode($arrData);
        }
    }
}
