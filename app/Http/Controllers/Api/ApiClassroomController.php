<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Services\ClassroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ApiClassroomController
{
    protected $classroomService;
    protected $userData;

    public function __construct(ClassroomService $classroomService, Request $request)
    {
        $this->classroomService = $classroomService;
        $this->userData = $request->{"USER_DATA"};
    }
    public function getAll()
    {
        try {
            $classrooms = $this->classroomService->getAllClassrooms();
            return Helper::composeReply('SUCCESS', 'Classrooms retrieved successfully', $classrooms);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'Error retrieving classrooms', ['error' => $e->getMessage()], 422);
        }
    }

    public function getById($id)
    {
        try {
            $classroom = $this->classroomService->getClassroomById($id);
            return Helper::composeReply('SUCCESS', 'Classroom retrieved successfully', $classroom);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'Error retrieving classroom', ['error' => $e->getMessage()], 422);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CLSRM_NAME' => 'required|string|unique:t_classrooms,CLSRM_NAME|max:80',
            'CLSRM_TYPE' => 'required|in:KB,SD',
            'CLSRM_GRADE' => 'required|string|max:2',
            'CLSRM_DESCRIPTION' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return Helper::composeReply('ERROR', 'Validation error', $validator->errors(), 422);
        }

        try {
            $data = $request->only(['CLSRM_NAME', 'CLSRM_TYPE', 'CLSRM_GRADE', 'CLSRM_DESCRIPTION']);
            $data['SYS_CREATE_USER'] = $this->userData->{"U_ID"};
            $classroom = $this->classroomService->createClassroom($data);
            return Helper::composeReply('SUCCESS', 'Classroom created successfully', $classroom);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'Error creating classroom', ['error' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'CLSRM_NAME' => 'sometimes|unique:t_classrooms,CLSRM_NAME|max:80',
            'CLSRM_TYPE' => 'sometimes|in:KB,SD',
            'CLSRM_GRADE' => 'sometimes|string|max:2',
            'CLSRM_DESCRIPTION' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return Helper::composeReply('ERROR', 'Validation error', $validator->errors(), 422);
        }

        try {
            $data = $request->only(['CLSRM_NAME', 'CLSRM_TYPE', 'CLSRM_GRADE', 'CLSRM_DESCRIPTION']);
            $data['SYS_UPDATE_USER'] = $this->userData->{"U_ID"};
            $classroom = $this->classroomService->updateClassroom($data, $id);
            return Helper::composeReply('SUCCESS', 'Classroom updated successfully', $classroom);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'Error updating classroom', ['error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $this->classroomService->deleteClassroom($id);
            return Helper::composeReply('SUCCESS', 'Classroom deleted successfully',null);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'Error deleting classroom', ['error' => $e->getMessage()], 422);
        }
    }
}
