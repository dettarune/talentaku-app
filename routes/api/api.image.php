<?php
use Illuminate\Support\Facades\Route;

    Route::get("image/", [\App\Http\Controllers\Api\ApiImageController::class, 'getImageByID'])->middleware("auth.api");


