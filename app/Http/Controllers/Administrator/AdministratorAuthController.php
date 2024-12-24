<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Support\Facades\Session;

class AdministratorAuthController
{
    public function showLogin(){
        if(Session::has("SESSION_USER_ID")) {
            return redirect('backend/dashboard');
        }
        else {
            $data = [];
            return view('login.index',$data);

        }
    }
}
