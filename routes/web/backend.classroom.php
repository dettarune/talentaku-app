<?php
use Illuminate\Support\Facades\Route;


Route::group(["prefix" => "backend/classroom", "middleware" => ["auth.web"]], function () {
    Route::get("/", [\App\Http\Controllers\Administrator\AdministratorClassroomController::class, 'index']);
//    Route::get("/{id}", [\App\Http\Controllers\Administrator\AdministratorClassroomController::class, 'getUser']);
    Route::match(["get", "post"], "datatables", [\App\Http\Controllers\Administrator\AdministratorClassroomController::class, 'getDatatable']);

});
