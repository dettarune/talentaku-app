<?php
use Illuminate\Support\Facades\Route;

Route::get("image/{url}", [\App\Http\Controllers\Api\ApiImageController::class, 'getUserProfileImage'])
    ->where('url', '.*')
    ->middleware("auth.api");
//Route::get("image/profile-student/{url}", [\App\Http\Controllers\Api\ApiImageController::class, 'getStudentProfileImage'])
//    ->where('url', '.*')
//    ->middleware("auth.api");
