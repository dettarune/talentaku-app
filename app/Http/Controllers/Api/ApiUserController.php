<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;

use App\Services\UserService;
use Illuminate\Http\Request;



class ApiUserController extends Controller
{
    protected $userData;
    protected $userService;

    public function __construct(Request $request, UserService $userService)
    {
        $this->userData = $request->{"USER_DATA"};
        $this->userService = $userService;
    }
    public function getUserById($U_ID){
        $user = $this->userService->getById($U_ID);
        return Helper::composeReply('SUCCESS','Success Get User By U_ID', $user);
    }
    public function getUserByToken($token){
        $user = $this->userService->getUserByLoginToken($token);
        return Helper::composeReply('SUCCESS','Success Get User By U_ID', $user);
    }
}
