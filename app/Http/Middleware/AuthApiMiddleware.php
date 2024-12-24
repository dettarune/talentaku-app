<?php
namespace App\Http\Middleware;

use Closure;
use App\Helpers\Helper;
use App\Services\UserService;

class AuthApiMiddleware
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //cek keberadaan login token di header dgn nama 'Talentaku-token'
        if(null !== $request->header("Talentaku-token")) {
            $loginToken = $request->header("Talentaku-token");
            $headerToken = $request->header("Talentaku-token");
        }
        //jika tidak ada, cek apakah ada parameter (GET/POST) dgn ``nama 'loginToken'
        elseif(null !== $request->{"loginToken"}) {
            $loginToken = $request->{"loginToken"};
            $parameterToken = $request->{"loginToken"};
        }
        //tidak ada parameter autorisasi yg disertakan => tolak akses
        else {
            return Helper::composeReply("ERROR", "[Middleware Protection 01] "."UnauthorizedAccess", array("API_ACTION" => "LOGOUT"));
        }

        $userData = $this->userService->getUserByLoginToken($loginToken);
        if(!$userData) {
            return Helper::composeReply("ERROR", "[Middleware Protection 02] ".trans("generic.UnauthorizedAccess"), array(
                "API_ACTION" => "LOGOUT",
                "TOKEN_SUSPECT" => $loginToken,
                "HEADER_TOKEN" => (isset($headerToken) ? $headerToken : null),
                "PARAMETER_TOKEN" => (isset($parameterToken) ? $parameterToken : null)
            ));
        }
        //lempar user data ke next
        $request->{"USER_DATA"} = $userData;

        return $next($request);
    }
}
