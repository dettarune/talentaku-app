<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\General\CustomAuthController;

Route::group(["prefix" => "backend/student", "middleware" => ["auth.web"]], function () {
    Route::get("/", [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'index']);
    Route::get("/{S_ID}", [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'show']);
    Route::post('/create', [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'store']);
    Route::post('/{id}/update', [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'update']);
    Route::post('/{id}/delete', [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'delete']);
    Route::post('/{id}/restore', [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'restoreStudent']);

    Route::post('/{id}/delete/report', [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'deleteStudentReport']);
    Route::match(["get", "post"], "datatables", [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'datatablesStudents']);

});

Route::group(["prefix" => "student-report", "middleware" => ["auth.web"]], function () {
    Route::match(["get", "post"], "datatables", [\App\Http\Controllers\Administrator\AdministratorStudentController::class, 'datatablesStudentsReport']);
});
