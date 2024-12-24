<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Repositories\StudentRepositoryInterface;
use App\Services\StudentService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Help;


class ApiStudentController
{
    protected $studentRepository;
    protected $userData;
    protected $userService;

    public function __construct(StudentService $studentRepository,
                                UserService $userService,
                                Request $request
    )
    {
        $this->studentRepository = $studentRepository;
        $this->userData = $request->{"USER_DATA"};
        $this->userService = $userService;
    }
    public function getAllStudents(Request $request){
        $className = $request->query('className');
        $classType = $request->query('classType');
        $classGrade = $request->query('classGrade');
        $parentId = $request->query('parentId');
        try {
            return Helper::composeReply('SUCCESS','Success get student',$this->studentRepository->getAll($className, $classType, $classGrade, $parentId));
        }catch (\Exception $exception){
            return Helper::composeReply('ERROR','ERROR get student',$exception->getMessage(),500);
        }
    }
    public function getById($S_ID)
    {
        $data = $this->studentRepository->getById($S_ID);
        return Helper::composeReply('SUCCESS','successfully retrieved',$data);
    }
    public function store(Request $request)
    {
        try {
              $request->validate([
                  'STUDENT_NAME' => 'required|string|max:80',
                  'STUDENT_ROLL_NUMBER' => 'nullable|string|max:80',
                  'STUDENT_PARENT_U_ID' => 'required|integer|exists:_users,U_ID|unique:t_students,STUDENT_PARENT_U_ID',
                  'STUDENT_SEX' => 'nullable|in:male,female,Not Specified',
                  'CLSRM_ID' => 'nullable|integer|exists:t_classrooms,CLSRM_ID',
                  'STUDENT_IMAGE_PROFILE' => 'nullable',
              ]);
            $data = [
                'STUDENT_NAME' => $request->STUDENT_NAME,
                'STUDENT_ROLL_NUMBER' => $request->STUDENT_ROLL_NUMBER,
                'STUDENT_PARENT_U_ID' => $request->STUDENT_PARENT_U_ID,
                'STUDENT_SEX' => $request->STUDENT_SEX,
                'CLSRM_ID' => $request->CLSRM_ID,
                'STUDENT_IMAGE_PROFILE' => $request->STUDENT_IMAGE_PROFILE,
                'SYS_CREATE_USER' => $this->userData->{"U_ID"},
            ];
            $student = $this->studentRepository->create($data);
            return Helper::composeReply('SUCCESS','successfully created',$student);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR','error created',['error' => $e->getMessage()],422);

        }
    }
    public function update(Request $request, $S_ID)
    {
        try {
            $request->validate([
                'STUDENT_NAME' => 'required|string|max:80',
                'STUDENT_ROLL_NUMBER' => 'nullable|string|max:80',
                'STUDENT_PARENT_U_ID' => 'required|integer|exists:_users,U_ID',
                'STUDENT_SEX' => 'nullable|in:male,female,Not Specified',
                'CLSRM_ID' => 'nullable|integer|exists:t_classrooms,CLSRM_ID',
                'STUDENT_IMAGE_PROFILE' => 'nullable',
            ]);
            $data = [
                'STUDENT_NAME' => $request->STUDENT_NAME,
                'STUDENT_ROLL_NUMBER' => $request->STUDENT_ROLL_NUMBER,
                'STUDENT_PARENT_U_ID' => $request->STUDENT_PARENT_U_ID,
                'STUDENT_SEX' => $request->STUDENT_SEX,
                'CLSRM_ID' => $request->CLSRM_ID,
                'STUDENT_IMAGE_PROFILE' => $request->STUDENT_IMAGE_PROFILE,
                'SYS_UPDATE_USER' => $this->userData->{"U_ID"},
            ];
            $student = $this->studentRepository->update($S_ID, $data);
            return Helper::composeReply('SUCCESS', 'successfully updated', $student);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'error updating', ['error' => $e->getMessage()], 422);
        }
    }

    public function delete($S_ID)
    {
        try {
            $this->studentRepository->delete($S_ID);
            return Helper::composeReply('SUCCESS', 'successfully deleted', []);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'error deleting', ['error' => $e->getMessage()], 422);
        }
    }
}
