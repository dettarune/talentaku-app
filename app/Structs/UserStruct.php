<?php
namespace App\Structs;

class UserStruct
{
    public $U_ID;
    public $U_NAME;
    public $U_PASSWORD_HASH;
    public $UR_ID;
    public $U_LOGIN_TOKEN;
    public $U_LOGIN_TIME;
    public $U_LOGIN_EXPIRED_TIME;
    public $SYS_CREATED_AT;
    public $SYS_CREATED_USER;
    public $SYS_UPDATED_AT;
    public $SYS_UPDATED_USER;
    public function __construct()
    {}
}
