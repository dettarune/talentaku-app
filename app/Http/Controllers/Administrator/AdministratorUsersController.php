<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\_user_roles;
use App\Models\Users;
use App\Services\ClassroomService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class AdministratorUsersController extends Controller
{
    protected $userData;
    protected $userService;
    protected $classroomService;

    public function __construct(Request $request, UserService $userService, ClassroomService $classroomService)
    {
        $this->userData = $request->{"USER_DATA"};
        $this->userService = $userService;
        $this->classroomService = $classroomService;
    }
    public function index(){
        $data["ctlUserData"] = $this->userData;
        $data['ctlNavMenuHeader'] = "User";
        $data["ctlTitle"] = "User";
        $data["token"] = $this->userData->{"U_LOGIN_TOKEN"};
        $groupedRole = _user_roles::all();
        $data["groupedRole"] = $groupedRole;
        $data["profileName"] = $this->userData->{"U_NAME"};


        return view('backend.user.index', $data);
    }
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'U_NAME' => 'required|unique:_users,U_NAME|max:80',
            'U_PASSWORD' => 'required|min:6',
            'UR_ID' => 'required|exists:_user_roles,UR_ID',
            'U_SEX' => 'required|in:Male,Female,Not Specified',
            'U_EMAIL' => 'nullable|email',
            'U_ADDRESS' => 'nullable|string|max:100',
            'U_PHONE' => 'nullable|string|max:20',
            'U_IMAGE_PROFILE' => 'nullable|file|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return Helper::composeReply('ERROR', $validator->errors()->all(),null,);
        }

        $data = [
            'U_NAME' => $request->U_NAME,
            'U_PASSWORD' => $request->U_PASSWORD,
            'UR_ID' => $request->UR_ID,
            'U_SEX' => $request->U_SEX,
            'U_EMAIL' => $request->U_EMAIL,
            'U_ADDRESS' => $request->U_ADDRESS,
            'U_PHONE' => $request->U_PHONE,
            'SYS_CREATE_USER' => $this->userData->{"U_ID"},
        ];
        if ($request->hasFile('U_IMAGE_PROFILE') && $request->file('U_IMAGE_PROFILE')->isValid()) {
            // Simpan file baru
            $imagePath = $request->file('U_IMAGE_PROFILE')->store('images', 'public');
            $data['U_IMAGE_PROFILE'] = $imagePath;
        } else {
            $data['U_IMAGE_PROFILE'] = null;
        }


        $newUser = $this->userService->create($data);
        return Helper::composeReply('SUCCESS', 'User created successfully', $newUser,201);
    }
    public function updateUser(Request $request, $U_ID)
    {
        $validator = Validator::make($request->all(), [
            'U_NAME' => 'sometimes|unique:_users,U_NAME|max:80',
            'U_PASSWORD' => 'sometimes|min:6',
            'UR_ID' => 'sometimes|exists:_user_roles,UR_ID',
            'U_SEX' => 'sometimes|in:Male,Female,Not Specified',
            'U_EMAIL' => 'sometimes|email',
            'U_ADDRESS' => 'sometimes|string|max:100',
            'U_PHONE' => 'sometimes|string|max:20',
            'U_IMAGE_PROFILE' => 'nullable|file|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            if ($validator->fails()) {
                return Helper::composeReply('ERROR', $validator->errors()->all(),null,);
            }
        }
        $data = [
            'U_NAME' => $request->U_NAME,
            'U_PASSWORD' => $request->U_PASSWORD,
            'UR_ID' => $request->UR_ID,
            'U_SEX' => $request->U_SEX,
            'U_EMAIL' => $request->U_EMAIL,
            'U_ADDRESS' => $request->U_ADDRESS,
            'U_PHONE' => $request->U_PHONE,
            'SYS_UPDATE_USER' => $this->userData->{"U_ID"},
        ];
        if ($request->hasFile('U_IMAGE_PROFILE') && $request->file('U_IMAGE_PROFILE')->isValid()) {
            // Cek apakah user memiliki file gambar sebelumnya
            $existingUser = $this->userService->getById($U_ID); // Pastikan getById mengembalikan user dengan U_IMAGE_PROFILE
            if ($existingUser && $existingUser->U_IMAGE_PROFILE) {
                // Hapus file lama dari storage
                Storage::disk('public')->delete($existingUser->U_IMAGE_PROFILE);
            }

            // Simpan file baru
            $imagePath = $request->file('U_IMAGE_PROFILE')->store('images', 'public');
            $data['U_IMAGE_PROFILE'] = $imagePath;
        } else {
            $data['U_IMAGE_PROFILE'] = null;
        }

        $updatedUser = $this->userService->update(array_filter($data), $U_ID);
        if (!$updatedUser) {
            return Helper::composeReply('ERROR', 'Failed Update User',null,404);
        }
        return Helper::composeReply('SUCCESS', 'User updated successfully', null);
    }

    public function datatablesUsers(Request $request)
    {
        $groupRole = isset($request->groupRole) ? $request->groupRole : "";

        $jsonData = $this->userService->getDatatables($groupRole);
        echo $jsonData;
    }

    public function delete($U_ID)
    {
        try {
           $data = $this->userService->delete($U_ID);
            return Helper::composeReply('SUCCESS', 'Success delete user',$data,);
        }catch (\Exception $e) {
            return Helper::composeReply('ERROR', $e->getMessage(),null,500);
        }
    }
}
