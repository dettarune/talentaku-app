<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;

use App\Models\Users;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ApiUserController extends Controller
{
    protected $userData;
    protected $userService;

    public function __construct(Request $request, UserService $userService)
    {
        $this->userData = $request->{"USER_DATA"};
        $this->userService = $userService;
    }
    public function getUserById(Request $request){
        $token = $request->header("Talentaku-token");
        $user = Users::with('role')
            ->where('U_LOGIN_TOKEN', $token)
            ->first();
        if($user->role->ROLE_NAME != 'RM_GUARDIAN'){
          $data = DB::table('_users as u')
                ->join('_user_roles as ur', 'ur.UR_ID', '=', 'u.UR_ID')
                ->join('t_students as s' , 's.STUDENT_PARENT_U_ID', '=', 'u.U_ID')
                ->join('t_classrooms as cl', 'cl.CLSRM_ID', '=', 's.CLSRM_ID')
                ->where('U_ID', $user->U_ID)
                ->select([
                    'u.U_ID',
                    'u.U_NAME',
                    'u.U_SEX',
                    'u.U_EMAIL',
                    'u.U_ADDRESS',
                    'u.U_IMAGE_PROFILE',
                    'u.U_PHONE',
                    'uR.ROLE_NAME',
                    'S.STUDENT_NAME',
                    'S.STUDENT_SEX',
                    'S.STUDENT_ROLL_NUMBER',
                    'S.STUDENT_IMAGE_PROFILE',
                    'cl.CLSRM_NAME',
                ])
                ->first();
          return Helper::composeReply('SUCCESS','Success Get User By U_ID', $data);
        }
        $data = DB::table('_users as u')
            ->join('_user_roles as ur', 'ur.UR_ID', '=', 'u.UR_ID')
            ->where('U_ID', $user->U_ID)
            ->select([
                'u.U_ID',
                'u.U_NAME',
                'u.U_SEX',
                'u.U_EMAIL',
                'u.U_ADDRESS',
                'u.U_IMAGE_PROFILE',
                'u.U_PHONE',
                'uR.ROLE_NAME',
            ])
            ->first();
        return Helper::composeReply('SUCCESS','Success Get User By U_ID', $data);
    }
    public function getUserByToken($token){
        $user = $this->userService->getUserByLoginToken($token);
        return Helper::composeReply('SUCCESS','Success Get User By U_ID', $user);
    }
}
