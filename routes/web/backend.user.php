<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\General\CustomAuthController;

Route::group(["prefix" => "backend/users", "middleware" => ["auth.web"]], function () {
    //info user
    Route::get("/", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'index']);
    Route::get("/{id}", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'getUser']);

    Route::post("/create", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'createUser']);
    Route::post("/{U_ID}/update", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'updateUser']);
    Route::post("/{U_ID}/delete", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'delete']);

    Route::match(["get", "post"], "datatables", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'datatablesUsers']);

});
