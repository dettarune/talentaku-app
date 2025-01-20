<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\t_students;
use App\Models\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Ramsey\Uuid\Uuid;
use function Laravel\Prompts\select;

class StudentRepository implements StudentRepositoryInterface
{

//    public function getAll()
//    {
//        $data = DB::table('t_students')->get();
//        return $data ?: null;
//    }

    public function getById($S_ID)
    {
        $data = DB::table('t_students')
            ->join('t_classrooms' ,'t_students.CLSRM_ID','=','t_classrooms.CLSRM_ID')
            ->join('_users','t_students.STUDENT_PARENT_U_ID','=','_users.U_ID')
            ->where('S_ID', $S_ID)
            ->select(
                't_students.*',
                't_classrooms.*',
                '_users.*',
            )
            ->first();
        return $data ?: null;
    }

    public function create(array $data)
    {
        $insertData = [
            'STUDENT_NAME' => $data['STUDENT_NAME'],
            'STUDENT_ROLL_NUMBER' => $data['STUDENT_ROLL_NUMBER'],
            'STUDENT_PARENT_U_ID' => $data['STUDENT_PARENT_U_ID'],
            'STUDENT_SEX' => $data['STUDENT_SEX'] ?? 'Not Specified',
            'STUDENT_IMAGE_PROFILE' => $data['STUDENT_IMAGE_PROFILE'],
            'CLSRM_ID' => $data['CLSRM_ID'] ?? null,
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_AT' => now(),
        ];
        try {
        $U_ID = DB::table('t_students')->insertGetId($insertData);
        return $this->getById($U_ID);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function update($S_ID, $attributes)
    {
        try {
            $student = t_students::find($S_ID);
            if (!$student) {
                return "Student not found.";
            }
            $student->STUDENT_NAME = $attributes['STUDENT_NAME'] ?? $student->STUDENT_NAME;
            $student->STUDENT_ROLL_NUMBER = $attributes['STUDENT_ROLL_NUMBER'] ?? $student->STUDENT_ROLL_NUMBER;
            $student->STUDENT_PARENT_U_ID = $attributes['STUDENT_PARENT_U_ID'] ?? $student->STUDENT_PARENT_U_ID;
            $student->STUDENT_SEX = $attributes['STUDENT_SEX'] ?? $student->STUDENT_SEX;
            $student->CLSRM_ID = $attributes['CLSRM_ID'] ?? $student->CLSRM_ID;
            $student->STUDENT_IMAGE_PROFILE = $attributes['STUDENT_IMAGE_PROFILE'] ?? $student->STUDENT_IMAGE_PROFILE;

            $student->save();
            return $this->getById($S_ID);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function delete($S_ID)
    {
        return t_students::where('S_ID','=',$S_ID)->delete();
    }

//    public function getByParentUID($parentUid)
//    {
//        $data = DB::table('t_students')
//            ->where('STUDENT_PARENT_U_ID', $parentUid)
//            ->get();
//        return $data ?: null;
//    }

    public function getByClassroomId($CLSRM_ID)
    {
        $data = DB::table('t_students')
            ->join('t_classrooms', 't_students.CLSRM_ID = t_classrooms.CLSRM_ID')
            ->select([
                't_students.*',
                't_classrooms.CLSRM_NAME',
                't_classrooms.CLSRM_TYPE',
                't_classrooms.CLSRM_GRADE'
            ])
            ->where('CLSRM_ID', $CLSRM_ID)
            ->get();
        return $data ?: null;
    }



    public function validateParentId($parentId)
    {
        return Users::join('_user_roles', '_users.UR_ID', '=', '_user_roles.UR_ID')
            ->where('_users.U_ID', $parentId)
            ->where('_user_roles.ROLE_NAME', 'RM_GUARDIAN')
            ->exists();
    }

//    public function getStudentByClassroomType($type)
//    {
//        return t_students::whereHas('classroom', function ($query) use ($type) {
//            $query->where('CLSRM_TYPE', $type);
//        })->get();
//    }

    public function getStudentByClassroomName($name)
    {
        $data = t_students::whereHas('classroom', function ($query) use ($name) {
            $query->where('CLSRM_NAME', $name);
        })->select('S_ID', 'STUDENT_NAME', 'STUDENT_ROLL_NUMBER', 'STUDENT_PARENT_U_ID', 'STUDENT_SEX', 'CLSRM_ID', 'STUDENT_IMAGE_PROFILE')->get();

        return $this->formatStudentResponse($data);
    }

//    public function getStudentByClassroomGrade($grade)
//    {
//        return t_students::whereHas('classroom', function ($query) use ($grade) {
//            $query->where('CLSRM_GRADE', $grade);
//        })->get();
//    }

    public function getAll()
    {
        $data = DB::table('t_students')
            ->select('S_ID', 'STUDENT_NAME', 'STUDENT_ROLL_NUMBER', 'STUDENT_PARENT_U_ID', 'STUDENT_SEX', 'CLSRM_ID', 'STUDENT_IMAGE_PROFILE')
            ->get();

        return $this->formatStudentResponse($data);
    }

    public function getByParentUID($parentUid)
    {
        $data = DB::table('t_students')
            ->select('S_ID', 'STUDENT_NAME', 'STUDENT_ROLL_NUMBER', 'STUDENT_PARENT_U_ID', 'STUDENT_SEX', 'CLSRM_ID', 'STUDENT_IMAGE_PROFILE')
            ->where('STUDENT_PARENT_U_ID', $parentUid)
            ->get();

        return $this->formatStudentResponse($data);
    }

    public function getStudentByClassroomGrade($grade)
    {
        $data = t_students::whereHas('classroom', function ($query) use ($grade) {
            $query->where('CLSRM_GRADE', $grade);
        })->select('S_ID', 'STUDENT_NAME', 'STUDENT_ROLL_NUMBER', 'STUDENT_PARENT_U_ID', 'STUDENT_SEX', 'CLSRM_ID', 'STUDENT_IMAGE_PROFILE')->get();

        return $this->formatStudentResponse($data);
    }

    public function getStudentByClassroomType($type)
    {
        $data = t_students::whereHas('classroom', function ($query) use ($type) {
            $query->where('CLSRM_TYPE', $type);
        })->select('S_ID', 'STUDENT_NAME', 'STUDENT_ROLL_NUMBER', 'STUDENT_PARENT_U_ID', 'STUDENT_SEX', 'CLSRM_ID', 'STUDENT_IMAGE_PROFILE')->get();

        return $this->formatStudentResponse($data);
    }

//    public function getStudentByClassroomName($name)
//    {
//        $data = t_students::whereHas('classroom', function ($query) use ($name) {
//            $query->where('CLSRM_NAME', $name);
//        })->select('S_ID', 'STUDENT_NAME', 'STUDENT_ROLL_NUMBER', 'STUDENT_PARENT_U_ID', 'STUDENT_SEX', 'CLSRM_ID', 'STUDENT_IMAGE_PROFILE')->get();
//
//        return $this->formatStudentResponse($data);
//    }

    /**
     * Helper function to format student response
     */
    private function formatStudentResponse($data)
    {
        return $data->map(function ($student) {
            $baseUrl = URL::to('/');
            return [
                'S_ID' => $student->S_ID,
                'STUDENT_NAME' => $student->STUDENT_NAME,
                'ROLL_NUMBER' => $student->STUDENT_ROLL_NUMBER,
                'PARENT_U_ID' => $student->STUDENT_PARENT_U_ID,
                'GENDER' => ucfirst($student->STUDENT_SEX),
                'CLASSROOM_ID' => $student->CLSRM_ID,
                'STUDENT_PROFILE_IMAGE' => $baseUrl.'/api/image/'.$student->STUDENT_IMAGE_PROFILE,
            ];
        });
    }

    public function getDatatables()
    {
        {
            $columns = array(
                0 => 'STUDENT_NAME',
                1 => 'STUDENT_ROLL_NUMBER',
                2 => 'STUDENT_PARENT',
                3 => 'STUDENT_SEX',
                4 => 'CLASSROOM_NAME',
            );

            $limit = $_POST['length'];
            $start = $_POST['start'];
            $orderColumnIndex = $_POST['order']['0']['column'] ?? null;
            if (isset($columns[$orderColumnIndex])) {
                $order = $columns[$orderColumnIndex];
            } else {
                $order = 'STUDENT_NAME';
            }

            $dir = $_POST['order']['0']['dir'] ?? 'asc';
//        $dir = $_POST['order']['0']['dir'];

            $baseData = DB::table("t_students")
                ->select([
                    "u.*", "cr.*", "t_students.*", "u.U_NAME as STUDENT_PARENT", "cr.CLSRM_NAME as CLASSROOM_NAME"
                ])
                ->join("t_classrooms as cr", "cr.CLSRM_ID", "=", "t_students.CLSRM_ID")
                ->join("_users as u", "u.U_ID", "=", "t_students.STUDENT_PARENT_U_ID");

            $baseCount = $baseData;
            $totalData = $baseCount->count();
            $totalFiltered = $totalData;
            if (empty($_POST['search']['value'])) {
                $baseData->orderBy($order, $dir);
                $baseData->limit($limit);
                $baseData->offset($start);
                $dtData = $baseData->get();
            } else {
                $search = $_POST['search']['value'];

                $baseData->where("t_students.STUDENT_NAME", "like", "%" . $search . "%");
                $baseData->orWhere("cr.CLSRM_NAME", "like", "%" . $search . "%");

                $filterCount = $baseData;

                $baseData->orderBy($order, $dir);
                $baseData->limit($limit);
                $baseData->offset($start);
                $dtData = $baseData->get();

                $totalFiltered = $filterCount->count();
                if (!($totalFiltered)) $totalFiltered = 0;
            }

            foreach ($dtData as $key => $value) {
                $nestedData["Student Name"] = "<span style='opacity: 0.8'>" . $value->{"STUDENT_NAME"} . "</span>";
                $nestedData["Roll Number"] = "<span style='opacity: 0.8'>" . $value->{"STUDENT_ROLL_NUMBER"} . "</span>";
                $nestedData["Parent"] = "<span style='opacity: 0.8'>" . $value->{"STUDENT_PARENT"} . "</span>";
                $nestedData["Gender"] = "<span style='opacity: 0.8'>" . $value->{"STUDENT_SEX"} . "</span>";
                $nestedData["Classroom"] = "<span style='opacity: 0.8'>" . $value->{"CLASSROOM_NAME"} . "</span>";
                $action = "";
                $action .= '
        <script type="text/javascript">
             var rowData_' . md5($value->{"S_ID"}) . ' = {
                "S_ID" : "' . $value->{"S_ID"} . '",
                "STUDENT_NAME" : "' . $value->{"STUDENT_NAME"} . '",
                "STUDENT_ROLL_NUMBER" : "' . $value->{"STUDENT_ROLL_NUMBER"} . '",
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
                            <a href="javascript:detailStudent(rowData_' . md5($value->{"S_ID"}) . ')" class="dropdown-item">
                            Detail Student
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
