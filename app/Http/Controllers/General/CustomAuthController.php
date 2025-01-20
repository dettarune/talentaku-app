<?php

namespace App\Http\Controllers\General;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\t_students;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CustomAuthController extends Controller
{
    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $admin =    $request->header("admin");
        $teacherId =    $request->header("teacher");
        $parentId =    $request->header("parent");

        // Validate input
        $validatedData = $request->validate([
            'U_NAME' => 'required|string|max:80',
            'U_PASSWORD' => 'required|string|max:80',
        ]);

        try {
            // Retrieve user from the database
            $user = Users::with('role')
                ->where('U_NAME', $validatedData['U_NAME'])
                ->first();
            log::info($user);

            // Check if user exists and password is correct
            if (!$user || !Hash::check($user->U_ID.$validatedData['U_PASSWORD'], $user->U_PASSWORD_HASH)) {
                return Helper::composeReply('ERROR', 'Invalid credentials', null ,401);
            }

           if($admin != null || $teacherId != null || $parentId != null){

            if($admin && $user->role->ROLE_NAME !== 'RM_ADMINISTRATOR'){
                return Helper::composeReply('ERROR', 'Role mismatch: Unauthorized access ' . $user->role->ROLE_NAME, null,403);
            }
            if($teacherId && $user->role->ROLE_NAME !== 'RM_TEACHER'){
                return Helper::composeReply('ERROR', 'Role mismatch: Unauthorized access ' .  $user->role->ROLE_NAME, null,403);
            }
            if($parentId && $user->role->ROLE_NAME !== 'RM_GUARDIAN'){
                return Helper::composeReply('ERROR', 'Role mismatch: Unauthorized access ' .  $user->role->ROLE_NAME, null,403);
            }
           }
            $students = [];
            if ($user->role->ROLE_NAME === 'RM_GUARDIAN') {
                $students = t_students::where('STUDENT_PARENT_U_ID', $user->U_ID)->first();
            }

            // Generate login token
            $token = $this->generateLoginToken($user->U_ID);
            $user->update(['U_LOGIN_TOKEN' => $token]);

            // Prepare user data for API response
            $response = [
                'USER' => [
                    'U_ID' => $user->U_ID,
                    'NAME' => $user->U_NAME,
                    'ROLE' => $user->role->ROLE_NAME,
                    'U_SEX' => $user->U_SEX,
                    'U_LOGIN_TIME' => now(),
                    'U_LOGIN_TOKEN' => $token,
                    'U_LOGIN_EXPIRED_TIME' => $user->U_LOGIN_EXPIRED_TIME,
                ],
            ];

            // Create session if requested
            if ($request->boolean('flagCreateSession')) {
                Session::put('SESSION_USER_NAME', $user->U_NAME);
                Session::put('SESSION_USER_ID', $user->U_ID);
                Session::put('SESSION_LOGIN_TOKEN', $token);
            }

            // Return success response
            return Helper::composeReply('SUCCESS', 'Login successfully', $response);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return Helper::composeReply('ERROR', 'An error occurred during login', $e->getMessage());
        }
    }


    /**
     * Generate a login token for the user.
     */
    private function generateLoginToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expirationTime = now()->addDays(floatval(Helper::getSetting('LOGIN_EXPIRED_DAYS')));
        try {
            DB::table('_users')->where('U_ID', $userId)->update([
                'U_LOGIN_TOKEN' => $token,
                'U_LOGIN_TIME' => now(),
                'U_LOGIN_EXPIRED_TIME' => $expirationTime ?? 1,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user token: ' . $e->getMessage());
            return false;
        }
        return $token;
    }

    /**
     * Logout the user (web or API).
     */
    public function logout(Request $request)
    {
        // Check for API logout
        $token = $request->header('TALENTA_TOKEN');
        if ($token) {
            DB::table('_users')
                ->where('U_LOGIN_TOKEN', $token)
                ->update([
                    'U_LOGIN_TOKEN' => null,
                    'U_LOGIN_EXPIRED_TIME' => null,
                    'U_LOGOUT_TIME' => now(),
                ]);

            return response()->json(['message' => 'Logout successful']);
        }

        // Handle web logout
        Session::flush();

        return redirect('/login');
    }

    /**
     * Middleware to validate the TALENTA_TOKEN for API requests.
     */
    public function validateApiToken(Request $request, \Closure $next)
    {
        $token = $request->header('TALENTA_TOKEN');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = DB::table('_users')
            ->where('U_LOGIN_TOKEN', $token)
            ->where('U_LOGIN_EXPIRED_TIME', '>', now())
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized or token expired'], 401);
        }

        // Pass user data for further processing
        $request->attributes->set('user', $user);

        return $next($request);
    }
}
