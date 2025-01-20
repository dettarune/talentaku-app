<?php

namespace App\Http\Controllers\Administrator;

use App\Models\t_classrooms;
use App\Models\t_students;
use App\Models\Users;
use App\Services\ClassroomService;
use App\Services\UserService;
use Illuminate\Http\Request;

class AdministratorDashboardController
{
    protected $userData;

    public function __construct(Request $request)
    {
        $this->userData = $request->{"USER_DATA"};
    }
    public function index(Request $request)
    {
        //USER_DATA diperoleh dari middleware AuthWebMiddleware
        $this->userData = $request->{"USER_DATA"};
        $student = t_students::count();
        $classroom = t_classrooms::count();
        $teacher = Users::whereHas('role', function ($query) {
            $query->where('ROLE_NAME', 'RM_TEACHER');
        })->count();
        $profileName = $this->userData->{'U_NAME'};
        $data = [
            'totalStudents' => $student,
            'totalClassrooms' => $classroom,
            'totalTeacher' => $teacher,
            'profileName' => $profileName
        ];

        return view('backend.dashboard.index', $data);
    }
}
