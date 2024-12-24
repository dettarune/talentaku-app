<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\General\CustomAuthController;

Route::group(["prefix" => "auth"], function () {
    //login user
    Route::post("/login", [CustomAuthController::class, 'login']);
    Route::get("token/validate", [CustomAuthController::class, 'validateApiToken']);

    //logout user
    Route::post("logout", [CustomAuthController::class, 'logout']);
    Route::get("logout", [CustomAuthController::class, 'logout']);
    Route::get('/generate-hash/{password}', function ($password) {
        return Hash::make($password);
    });


    Route::post("reset/password/{U_ID}", [CustomAuthController::class, 'resetPassword']);
});
