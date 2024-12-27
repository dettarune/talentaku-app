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
            ->where('S_ID', $S_ID)
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
            'CLSRM_ID' => $data['CLSRM_ID'] ?? null,
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_AT' => now(),
        ];
        try {
            if (!empty($data['STUDENT_IMAGE_PROFILE'])) {
                $file = $data['STUDENT_IMAGE_PROFILE'];
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('uploads/images', $fileName, 'public');
                $insertData['STUDENT_IMAGE_PROFILE'] = $filePath;
            }
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
            if (!empty($attributes['STUDENT_IMAGE_PROFILE'])) {
                $base64Data = $attributes['STUDENT_IMAGE_PROFILE'];
                $mimeType = Helper::getMimeTypeFromBase64($base64Data);
                $mediaContentValue = Helper::removeBase64Header($base64Data);
                if (empty($student->STUDENT_IMAGE_PROFILE)) {
                    $mediaId = Uuid::uuid4()->toString();
                    DB::table('_medias')->insert([
                        'MEDIA_ID' => $mediaId,
                        'MEDIA_MIME_TYPE' => $mimeType,
                        'MEDIA_CONTENT_TYPE' => 'Base64',
                        'MEDIA_CONTENT_VALUE' => $mediaContentValue,
                        'SYS_CREATED_AT' => now(),
                        'SYS_CREATED_USER' => $attributes['SYS_UPDATE_USER'] ?? 'System',
                    ]);
                    $student->STUDENT_IMAGE_PROFILE = $mediaId;
                } else {
                    // Update existing media record
                    DB::table('_medias')->where('MEDIA_ID', $student->STUDENT_IMAGE_PROFILE)->update([
                        'MEDIA_MIME_TYPE' => $mimeType,
                        'MEDIA_CONTENT_VALUE' => $mediaContentValue,
                        'SYS_UPDATE_AT' => now(),
                        'SYS_UPDATED_USER' => $attributes['SYS_UPDATE_USER'] ?? 'System',
                    ]);
                }
            }
            $student->save();
            return $this->getById($S_ID);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function delete($S_ID)
    {
        return t_students::destroy($S_ID);
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

}
