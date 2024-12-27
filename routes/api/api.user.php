<?php
use Illuminate\Support\Facades\Route;


Route::group(["prefix" => "users"], function() {
    //info user
    Route::get("/", [\App\Http\Controllers\Api\ApiUserController::class, 'getUserById'])->middleware("auth.api");
    Route::get("/token /{token}", [\App\Http\Controllers\Api\ApiUserController::class, 'getUserByToken'])->middleware("auth.api");
});


