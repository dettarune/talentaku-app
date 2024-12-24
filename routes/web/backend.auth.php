<?php

use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "backend/auth", ], function() {
    Route::get("/", [\App\Http\Controllers\Administrator\AdministratorAuthController::class, 'showLogin']);
    Route::get("login", [\App\Http\Controllers\Administrator\AdministratorAuthController::class, 'showLogin']);

    //login user
    Route::post("login", [\App\Http\Controllers\General\CustomAuthController::class, 'login']);

    //logout user
});
Route::get("backend/auth/logout", [\App\Http\Controllers\Administrator\AdministratorAuthController::class, 'logout']);
