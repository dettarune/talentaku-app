<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\General\CustomAuthController;

Route::group(["prefix" => "backend/students", "middleware" => ["auth.web"]], function () {
    Route::get("/", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'index']);
    Route::get("/{id}", [\App\Http\Controllers\Administrator\AdministratorUsersController::class, 'getUser']);
});
