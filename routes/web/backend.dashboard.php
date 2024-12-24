<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Administrator\AdministratorDashboardController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "backend/dashboard", "middleware" => ["auth.web"]], function() {
    Route::get("/", [AdministratorDashboardController::class, 'index']);
});

