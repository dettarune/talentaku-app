<?php

use Illuminate\Support\Facades\Route;


Route::group(["prefix" => "classroom", "middleware" => ["auth.api"]], function () {
    Route::get("/", [\App\Http\Controllers\Api\ApiClassroomController::class, 'getAll']);
    Route::get("/{id}", [\App\Http\Controllers\Api\ApiClassroomController::class, 'getById']);
    Route::post("/create", [\App\Http\Controllers\Api\ApiClassroomController::class, 'store']);
    Route::post("/{id}/update", [\App\Http\Controllers\Api\ApiClassroomController::class, 'update']);
});
