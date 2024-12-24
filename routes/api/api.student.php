<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\General\CustomAuthController;

Route::group(["prefix" => "student", "middleware" => ["auth.api"]], function () {
    Route::get("/", [\App\Http\Controllers\Api\ApiStudentController::class, 'getAllStudents']);
    Route::get("/{S_ID}", [\App\Http\Controllers\Api\ApiStudentController::class, 'getById']);
    Route::post("/create", [\App\Http\Controllers\Api\ApiStudentController::class, 'store']);

});
