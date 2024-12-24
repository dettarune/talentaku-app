<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;

class AdministratorDashboardController
{
    protected $userData;

    public function index(Request $request)
    {
        //USER_DATA diperoleh dari middleware AuthWebMiddleware
        $this->userData = $request->{"USER_DATA"};
        $data = [];
        return view('backend.dashboard.index', $data);
    }
}
