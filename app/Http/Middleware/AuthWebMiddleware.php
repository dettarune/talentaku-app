<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Services\UserService;

class AuthWebMiddleware
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Session::has("SESSION_USER_ID")) { //tidak ada session
            //hapus session (?)
            Session::flush();

            //kick
            if($request->ajax()){
                return Helper::composeReply("ERROR", trans("generic.AuthLoginNeeded"), null);
            }
            else {
                return redirect()->to('backend/auth')->withErrors([trans("generic.AuthLoginNeeded")]);
            }
        }
        $userData = $this->userService->getById(Session::get("SESSION_USER_ID", ""));
        if(!$userData) { //data user tidak ada
            //hapus session
            Session::flush();

            //kick!
            if($request->ajax()){
                return Helper::composeReply("ERROR", trans("generic.AuthLoginNeeded"), null);
            }
            else {
                return redirect()->to('backend/auth')->withErrors([trans("generic.AuthLoginNeeded")]);
            }
        }

        //lempar user data ke next
        $request->{"USER_DATA"} = $userData;
        return $next($request);
    }
}
