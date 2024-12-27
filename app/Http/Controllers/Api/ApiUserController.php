<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;

use App\Models\Users;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;


class ApiUserController extends Controller
{
    protected $userData;
    protected $userService;

    public function __construct(Request $request, UserService $userService)
    {
        $this->userData = $request->{"USER_DATA"};
        $this->userService = $userService;
    }
    public function getUserById(Request $request)
    {
        // Mendapatkan token dari header
        $token = $request->header("Talentaku-token");

        // Validasi token, pastikan token ada
        if (!$token) {
            return Helper::composeReply('ERROR', 'Token is missing', null);
        }

        // Cari user berdasarkan token
        $user = Users::with('role')->where('U_LOGIN_TOKEN', $token)->first();

        // Validasi user
        if (!$user) {
            return Helper::composeReply('ERROR', 'User not found', null);
        }

            $baseUrl = URL::to('/');
        // Jika role adalah RM_GUARDIAN
        if ($user->role->ROLE_NAME === 'RM_GUARDIAN') {
            Log::info('User is RM_GUARDIAN: ' . $token);

            // Query data untuk RM_GUARDIAN
            $data = DB::table('_users as u')
                ->join('_user_roles as ur', 'ur.UR_ID', '=', 'u.UR_ID')
                ->leftJoin('t_students as s', 's.STUDENT_PARENT_U_ID', '=', 'u.U_ID')
                ->leftJoin('t_classrooms as cl', 'cl.CLSRM_ID', '=', 's.CLSRM_ID')
                ->where('u.U_LOGIN_TOKEN', $token)
                ->select([
                    'u.U_ID',
                    'u.U_NAME',
                    'u.U_SEX',
                    'u.U_EMAIL',
                    'u.U_ADDRESS',
                    DB::raw("IF(u.U_IMAGE_PROFILE IS NOT NULL, CONCAT('$baseUrl/api/image/', u.U_IMAGE_PROFILE), NULL) as U_IMAGE_PROFILE"),
                    'u.U_PHONE',
                    'ur.ROLE_NAME',
                    DB::raw('COALESCE(s.STUDENT_NAME, "N/A") as STUDENT_NAME'),
                    DB::raw('COALESCE(cl.CLSRM_NAME, "N/A") as CLASSROOM_NAME'),
                ])
                ->first();

            // Cek apakah data ditemukan
            if (!$data) {
                return Helper::composeReply('ERROR', 'No data found for RM_GUARDIAN', null);
            }

            return Helper::composeReply('SUCCESS', 'Success Get User By U_ID', $data);
        }

        // Jika role bukan RM_GUARDIAN, ambil data user biasa
        $data = DB::table('_users as u')
            ->join('_user_roles as ur', 'ur.UR_ID', '=', 'u.UR_ID')
            ->where('u.U_ID', $user->U_ID)
            ->select([
                'u.U_ID as U_ID',
                'u.U_NAME as U_NAME',
                'u.U_SEX as U_SEX',
                'u.U_EMAIL as U_EMAIL',
                'u.U_ADDRESS as U_ADDRESS',
                DB::raw("IF(u.U_IMAGE_PROFILE IS NOT NULL, CONCAT('$baseUrl/api/image/', u.U_IMAGE_PROFILE), NULL) as U_IMAGE_PROFILE"),
                'u.U_PHONE as U_PHONE',
                'ur.ROLE_NAME as ROLE_NAME',
            ])
            ->first();

        // Cek apakah data ditemukan
        if (!$data) {
            return Helper::composeReply('ERROR', 'No data found for user', null);
        }

        return Helper::composeReply('SUCCESS', 'Success Get User By U_ID', $data);
    }

    public function getUserByToken($token){
        $user = $this->userService->getUserByLoginToken($token);
        return Helper::composeReply('SUCCESS','Success Get User By U_ID', $user);
    }
}
