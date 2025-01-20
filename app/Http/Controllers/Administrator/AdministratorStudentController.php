<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\Helper;
use App\Models\_user_roles;
use App\Models\t_classrooms;
use App\Models\t_student_reports;
use App\Models\t_students;
use App\Models\Users;
use App\Services\ClassroomService;
use App\Services\StudentReportService;
use App\Services\StudentService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isEmpty;

class AdministratorStudentController
{
    protected $userData;
    protected $userService;
    protected $classroomService;
    protected $studentService;
    protected $studentReportService;

    public function __construct(Request $request, UserService $userService, ClassroomService $classroomService, StudentService $studentService, StudentreportService $studentReportService )
    {
        $this->userData = $request->{"USER_DATA"};
        $this->userService = $userService;
        $this->classroomService = $classroomService;
        $this->studentService = $studentService;
        $this->studentReportService = $studentReportService;
    }
    public function index(){
        $data["ctlUserData"] = $this->userData;
        $data['ctlNavMenuHeader'] = "User";
        $data["ctlTitle"] = "User";
        $data["token"] = $this->userData->{"U_LOGIN_TOKEN"};
        $groupedRole = _user_roles::all();
        $data["groupedRole"] = $groupedRole;
        $data["profileName"] = $this->userData->{"U_NAME"};

        // Orang tua yang sudah terpakai oleh siswa
        $data["usedParents"] = Users::where('UR_ID', '2')
            ->whereIn('U_ID', function ($query) {
                $query->select('STUDENT_PARENT_U_ID')->from('t_students');
            })
            ->get();

        // Orang tua yang belum terpakai oleh siswa
        $data["unusedParents"] = Users::where('UR_ID', '2')
            ->whereNotIn('U_ID', function ($query) {
                $query->select('STUDENT_PARENT_U_ID')->from('t_students');
            })
            ->get();

        $data["classrooms"] = t_classrooms::all();

        return view('backend.student.index', $data);
    }

    public function show($S_ID)
    {
        $data = t_students::with(['classroom' => function($query) {
            $query->select('CLSRM_ID', 'CLSRM_NAME');
        },
            'parent' => function($query) {
                $query->select('U_ID', 'U_NAME', 'U_EMAIL');
            }])
            ->withTrashed()
            ->find($S_ID);

        $token = $this->userData->{"U_LOGIN_TOKEN"};
        $profileName = $this->userData->{"U_NAME"};
        if (!$data) {
            return redirect()->back()->with('error', 'Student not found.');
        }
        $usedParents = Users::where('UR_ID', '2')
            ->whereIn('U_ID', function ($query) {
                $query->select('STUDENT_PARENT_U_ID')->from('t_students');
            })
            ->get();

        // Orang tua yang belum terpakai oleh siswa
        $unusedParents = Users::where('UR_ID', '2')
            ->whereNotIn('U_ID', function ($query) {
                $query->select('STUDENT_PARENT_U_ID')->from('t_students');
            })
            ->get();

        $classrooms = t_classrooms::all();
//        dd($data);
        $student = $data;
        return view('backend.student.detail', [
            'student' => $student,
            'token' => $token,
            'profileName' => $profileName,
            'usedParents' => $usedParents,
            'unusedParents' => $unusedParents,
            'classrooms' => $classrooms,
        ]);
    }

    public function getById($S_ID)
    {
        $data = $this->studentService->getById($S_ID);
        return Helper::composeReply('SUCCESS','successfully retrieved',$data);
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'STUDENT_NAME' => 'required|string|max:80',
                'STUDENT_ROLL_NUMBER' => 'nullable|integer|max:80',
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
                'SYS_CREATE_USER' => $this->userData->{"U_ID"},
            ];
            if ($request->hasFile('STUDENT_IMAGE_PROFILE') && $request->file('STUDENT_IMAGE_PROFILE')->isValid()) {
                $imagePath = $request->file('STUDENT_IMAGE_PROFILE')->store('images', 'public');
                $data['STUDENT_IMAGE_PROFILE'] = $imagePath;
            } else {
                $data['STUDENT_IMAGE_PROFILE'] = null;
            }
            $student = $this->studentService->create($data);
            return Helper::composeReply('SUCCESS','successfully created',$student);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR','error created',['error' => $e->getMessage()],422);

        }
    }
    public function update(Request $request, $S_ID)
    {
        try {
            $request->validate([
                'STUDENT_NAME' => 'nullable|string|max:80',
                'STUDENT_ROLL_NUMBER' => 'nullable|integer|max:80',
                'STUDENT_PARENT_U_ID' => 'nullable|integer|exists:_users,U_ID',
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
                'SYS_UPDATE_USER' => $this->userData->{"U_ID"},
            ];
            if ($request->hasFile('STUDENT_IMAGE_PROFILE') && $request->file('STUDENT_IMAGE_PROFILE')->isValid()) {
                // Cek apakah user memiliki file gambar sebelumnya
                $existingUser = $this->studentService->getById($S_ID); // Pastikan getById mengembalikan user dengan STUDENT_IMAGE_PROFILE
                if ($existingUser && $existingUser->STUDENT_IMAGE_PROFILE) {
                    // Hapus file lama dari storage
                    Storage::disk('public')->delete($existingUser->STUDENT_IMAGE_PROFILE);
                }

                // Simpan file baru
                $imagePath = $request->file('STUDENT_IMAGE_PROFILE')->store('images', 'public');
                $data['STUDENT_IMAGE_PROFILE'] = $imagePath;
            } else {
                $data['STUDENT_IMAGE_PROFILE'] = null;
            }
            $student = $this->studentService->update($S_ID, $data);
            return Helper::composeReply('SUCCESS', 'successfully updated', $student);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'error updating', ['error' => $e->getMessage()], 422);
        }
    }

    public function delete($id)
    {
        try {
            $this->studentService->delete($id);
            return Helper::composeReply('SUCCESS', 'successfully deleted', []);
        } catch (\Exception $e) {
            return Helper::composeReply('ERROR', 'error deleting', ['error' => $e->getMessage()], 422);
        }
    }
    public function datatablesStudents(Request $request)
    {
//        $groupRole = isset($request->groupRole) ? $request->groupRole : "";

        $jsonData = $this->studentService->getDatatables();
        echo $jsonData;
    }

    public function datatablesStudentsReport(Request $request)
    {
        $S_ID = isset($request->student_id) ? $request->student_id : "";
        $date = isset($request->date) ? $request->date : null;

        $jsonData = $this->studentReportService->getDatatables($S_ID, );
        echo $jsonData;
    }
    public function restoreStudent($id)
    {
        $student = t_students::withTrashed()->find($id);

        if ($student) {
            $student->deleted_at = null;
            $student->save();

            return response()->json([
                'STATUS' => 'SUCCESS',
                'MESSAGE' => 'Student restored successfully.',
                'PAYLOAD' => $student
            ], 200);
        }

        return response()->json(['MESSAGE' => 'Student not found or already restored.'], 404);
    }
    public function deleteStudentReport($id)
    {
        if (empty($id)) {
            return response()->json([
                'STATUS' => 'ERROR',
                'MESSAGE' => 'Student report ID is invalid or missing.',
                'PAYLOAD' => null
            ], 404);
        }
        // Hapus laporan yang terkait dengan student
        $deletedReports = t_student_reports::find($id)->delete();

        // Cek apakah ada laporan yang dihapus
        if ($deletedReports) {
            return response()->json([
                'STATUS' => 'SUCCESS',
                'MESSAGE' => 'Student reports deleted successfully.',
                'PAYLOAD' => $deletedReports
            ]);
        } else {
            return response()->json([
                'STATUS' => 'ERROR',
                'MESSAGE' => 'No reports found for the student.'
            ], 404);
        }
    }

}
