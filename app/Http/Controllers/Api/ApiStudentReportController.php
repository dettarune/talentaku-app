<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Services\StudentReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiStudentReportController
{
    protected $studentReportService;
    protected $userData;

    public function __construct(StudentReportService $studentReportService,Request $request)
    {
        $this->studentReportService = $studentReportService;
        $this->userData = $request->{"USER_DATA"};
    }
    public function getStudentReport(Request $request){
        $studentId = $request->query('studentId');
        $teacherId = $request->query('teacherId');
        $parentId = $request->query('parentId');
        try {
        return Helper::composeReply('SUCCESS','Success get report',$this->studentReportService->getAllReport($studentId, $teacherId, $parentId));
        }catch (\Exception $exception){
            return Helper::composeReply('ERROR','ERROR get report',$exception->getMessage(),500);
        }
    }

    public function getStudentReportDetailById(Request $request, $id){
        try {
            return Helper::composeReply('SUCCESS','Success get detail report',$this->studentReportService->getReportById($id));
        }catch (\Exception $exception){
            return Helper::composeReply('ERROR','ERROR get detail report',$exception->getMessage(),500);
        }
    }
    public function createStudentReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'S_ID' => 'required|exists:t_students,S_ID',
            'U_ID' => 'required|exists:_users,U_ID',
            'SR_CONTENT' => 'required|string|max:1000',
            'SR_DATE' => 'required|date',
            'ACTIVITIES' => 'nullable|array',
            'ACTIVITIES.*.ACTIVITY_TYPE' => 'required_with:ACTIVITIES|string',
            'ACTIVITIES.*.ACTIVITY_NAME' => 'required_with:ACTIVITIES|string',
            'ACTIVITIES.*.STATUS' => 'required_with:ACTIVITIES|in:MUNCUL,KURANG,BELUM MUNCUL',
        ]);

        if ($validator->fails()) {
            return Helper::composeReply('ERROR', 'Validation failed', $validator->errors()->all(), 422);
        }

        $data = [
            'S_ID' => $request->S_ID,
            'U_ID' => $request->U_ID,
            'SR_CONTENT' => $request->SR_CONTENT,
            'SR_DATE' => $request->SR_DATE,
            'SYS_CREATE_USER' => $this->userData->{"U_ID"},
            'ACTIVITIES' => $request->ACTIVITIES,
        ];

        try {
            // Optionally, check for duplicate reports
//             $existingReport = $this->studentReportService->findSimilarReport($data);
//             if ($existingReport) {
//                 return Helper::composeReply('ERROR', 'Duplicate report found', null, 409);
//             }

            $newReport = $this->studentReportService->createReport($data);
            return Helper::composeReply('SUCCESS', 'Student report created successfully', $newReport, 201);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'Failed to create report', $e->getMessage(), 500);
        }
    }
    public function updateStudentReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'SR_CONTENT' => 'sometimes|required|string|max:1000',
            'SR_DATE' => 'sometimes|required|date',
            'ACTIVITIES' => 'nullable|array',
            'ACTIVITIES.*.ACTIVITY_TYPE' => 'required_with:ACTIVITIES|string',
            'ACTIVITIES.*.ACTIVITY_NAME' => 'required_with:ACTIVITIES|string',
            'ACTIVITIES.*.STATUS' => 'required_with:ACTIVITIES|in:MUNCUL,KURANG,BELUM MUNCUL',
        ]);
        if ($validator->fails()) {
            return Helper::composeReply('ERROR', 'Validation failed', $validator->errors()->all(), 422);
        }
        $data = $request->only(['SR_CONTENT', 'SR_DATE', 'ACTIVITIES']);
        $data['SYS_UPDATE_USER'] = $this->userData->{"U_ID"};
        try {
            $updatedReport = $this->studentReportService->updateReport($data, $id);
            return Helper::composeReply('SUCCESS', 'Student report updated successfully', $updatedReport, 200);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'Failed to update report', $e->getMessage(), 500);
        }
    }
}
