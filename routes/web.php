<?php

use App\Helpers\Helper;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect('backend/auth');
})->name('public');

Route::match(["get", "post"], "token", function() {
    return Helper::composeReply("SUCCESS", "Token", csrf_token());
});

require __DIR__ . '/web/backend.auth.php';
require __DIR__ . '/web/backend.dashboard.php';
require __DIR__ . '/web/backend.user.php';
require __DIR__ . '/web/backend.classroom.php';

