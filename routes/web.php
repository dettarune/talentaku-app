<?php

use App\Helpers\Helper;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    //return view('welcome');
    return redirect('backend/auth');
})->name('public');

Route::match(["get", "post"], "token", function() {
    return Helper::composeReply("SUCCESS", "Token", csrf_token());
});


Route::post('/logout', function (\Illuminate\Http\Request $request) {
    try {
        $userId = Session::get('SESSION_USER_ID');
        $token = Session::get('SESSION_LOGIN_TOKEN');

        if ($userId && $token) {
            // Update user token to null in the database
            \App\Models\Users::where('U_ID', $userId)->update(['U_LOGIN_TOKEN' => null]);

            // Clear session
            Session::forget(['SESSION_USER_NAME', 'SESSION_USER_ID', 'SESSION_LOGIN_TOKEN']);
            Session::flush();

            Log::info("User with ID {$userId} logged out successfully.");

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Logged out successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'ERROR',
            'message' => 'No active session found',
        ], 400);
    } catch (\Exception $e) {
        Log::error('Logout error: ' . $e->getMessage());

        return response()->json([
            'status' => 'ERROR',
            'message' => 'An error occurred during logout',
            'error' => $e->getMessage(),
        ], 500);
    }
});


require __DIR__ . '/web/backend.auth.php';
require __DIR__ . '/web/backend.dashboard.php';
require __DIR__ . '/web/backend.user.php';
require __DIR__ . '/web/backend.classroom.php';
require __DIR__ . '/web/backend.student.php';

