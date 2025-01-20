<?php

use App\Http\Controllers\General\CustomAuthController;
use App\Models\t_student_reports;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function(Request $request) {
    $id = $request->query('t');
    return t_student_reports::whereHas('student', function ($query) use ($id){
        $query->where('STUDENT_PARENT_U_ID', 1);
    })->first();
});





require __DIR__ . '/api/api.auth.php';
require __DIR__ . '/api/api.user.php';
require __DIR__ . '/api/api.image.php';
require __DIR__ . '/api/api.classroom.php';
require __DIR__ . '/api/api.student.php';
require __DIR__ . '/api/api.student.report.php';
