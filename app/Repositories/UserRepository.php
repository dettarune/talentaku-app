<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class UserRepository implements UserRepositoryInterface
{

    public function getAll()
    {
        $data = DB::table('_users')
            ->join('_user_roles as ur', 'ur.UR_ID', '=', '_users.UR_ID')
            ->get();
        return $data ?: null;
    }

    public function getById($U_ID)
    {
        $data = DB::table('_users')
            ->join('_user_roles as ur', 'ur.UR_ID', '=', '_users.UR_ID')
            ->where('U_ID', $U_ID)
            ->first();
        return $data ?: null;
    }

    public function create(array $data)
    {

        $insertData = [
            'U_NAME' => $data['U_NAME'],
            'U_PASSWORD_HASH' => Hash::make($data['U_PASSWORD']),
            'UR_ID' => $data['UR_ID'],
            'U_SEX' => $data['U_SEX'] ?? 'Not Specified',
            'U_EMAIL' => $data['U_EMAIL'],
            'U_ADDRESS' => $data['U_ADDRESS'],
            'U_PHONE' => $data['U_PHONE'],
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_TIME' => now(),
        ];
        if(!empty($data['U_IMAGE_PROFILE'])){
                $base64Data = $data['U_IMAGE_PROFILE'];
                $mimeType = Helper::getMimeTypeFromBase64($base64Data);
                $mediaId = Uuid::uuid4()->toString();
                $insertData['U_IMAGE_PROFILE'] = $mediaId;
                $mediaId = DB::table('_medias')->insertGetId([
                    'MEDIA_ID' => $mediaId,
                    'MEDIA_MIME_TYPE' => $mimeType,
                    'MEDIA_CONTENT_TYPE' => 'Base64',
                    'MEDIA_CONTENT_VALUE' => Helper::removeBase64Header($base64Data),
                    'SYS_CREATE_AT' => now(),
                    'SYS_CREATED_USER' => $data['SYS_CREATE_USER'] ?? 'System',
                ]);
        }
        $U_ID = DB::table('_users')->insertGetId($insertData);
        return $this->getById($U_ID);
    }

    public function update(array $data, $U_ID)
    {
        $user = $this->getById($U_ID);
        if (!$user) {
            return null;
        }

        $updateData = [
            'SYS_UPDATE_USER' => $data['SYS_UPDATE_USER'] ?? 'System',
            'SYS_UPDATE_TIME' => now(),
        ];
        if (isset($data['U_NAME'])) {
            $updateData['U_NAME'] = $data['U_NAME'];
        }
        if (isset($data['U_PASSWORD'])) {
            $updateData['U_PASSWORD_HASH'] = Hash::make($data['U_PASSWORD']);
        }
        if (isset($data['UR_ID'])) {
            $updateData['UR_ID'] = $data['UR_ID'];
        }
        if (isset($data['U_SEX'])) {
            $updateData['U_SEX'] = $data['U_SEX'];
        }
        if (isset($data['U_EMAIL'])) {
            $updateData['U_EMAIL'] = $data['U_EMAIL'];
        }
        if (isset($data['U_ADDRESS'])) {
            $updateData['U_ADDRESS'] = $data['U_ADDRESS'];
        }
        if (isset($data['U_PHONE'])) {
            $updateData['U_PHONE'] = $data['U_PHONE'];
        }
        if (isset($data['U_IMAGE_PROFILE'])) {
            $updateData['U_IMAGE_PROFILE'] = $data['U_IMAGE_PROFILE'];
        }
        DB::table('_users')->where('U_ID', $U_ID)->update($updateData);
        return $this->getById($U_ID);
    }

    public function delete($U_ID)
    {
        return Users::destroy('U_ID', $U_ID);
    }

    public function getUserByLoginToken($token)
    {
        $data = DB::table('_users')
            ->join('_user_roles as ur', 'ur.UR_ID', '=', '_users.UR_ID')
            ->where('U_LOGIN_TOKEN', $token)
            ->where('U_LOGIN_EXPIRED_TIME', '>', now())
            ->first();
        return $data ?: null;
    }

    public function getDatatables($role)
    {
        $columns = array(
            0 => 'U_NAME',
            1 => 'U_SEX',
            2 => 'ROLE_NAME'
        );

        $limit = $_POST['length'];
        $start = $_POST['start'];
        $orderColumnIndex = $_POST['order']['0']['column'] ?? null;
        if (isset($columns[$orderColumnIndex])) {
            $order = $columns[$orderColumnIndex];
        } else {
            $order = 'U_NAME';
        }

        $dir = $_POST['order']['0']['dir'] ?? 'asc';
//        $dir = $_POST['order']['0']['dir'];

        $baseData = DB::table("_users")
            ->select([
                "_users.U_ID", "_users.U_NAME","_users.U_SEX","_users.U_LOGIN_TIME",'ur.ROLE_NAME'
            ])
            ->join("_user_roles as ur", "ur.UR_ID", "=", "_users.UR_ID");

        if(isset($role) && $role != "") {
            $baseData->where("ur.UR_ID", "=", $role);
        }
        $baseCount = $baseData;
        $totalData = $baseCount->count();
        $totalFiltered = $totalData;
        if(empty($_POST['search']['value'])) {
            $baseData->orderBy($order, $dir);
            $baseData->limit($limit);
            $baseData->offset($start);
            $dtData = $baseData->get();
        }
        else {
            $search = $_POST['search']['value'];

            $baseData->where("_users.U_NAME", "like", "%".$search."%");
            $baseData->orWhere("ur.ROLE_NAME", "like", "%".$search."%");

            $filterCount = $baseData;

            $baseData->orderBy($order, $dir);
            $baseData->limit($limit);
            $baseData->offset($start);
            $dtData = $baseData->get();

            $totalFiltered = $filterCount->count();
            if(!($totalFiltered)) $totalFiltered = 0;
        }

        foreach ($dtData as $key => $value) {
            $nestedData["User Name"] = "<span style='opacity: 0.8'>".$value->{"U_NAME"}."</span>";
            $nestedData["User Sex"] = "<span style='opacity: 0.8'>".$value->{"U_SEX"}."</span>";
            $nestedData["User Role"] = $value->{"ROLE_NAME"};
            $nestedData["Last Login"] = $value->{"U_LOGIN_TIME"};
            $action = "";
            $action .= '
        <script type="text/javascript">
            var rowData_'.md5($value->{"U_ID"}).' = {
                "U_ID" : "'.$value->{"U_ID"}.'",
                "U_NAME" : "'.$value->{"U_NAME"}.'",
                "U_SEX" : "'.$value->{"U_SEX"}.'",
                "ROLE_NAME" : "'.$value->{"ROLE_NAME"}.'",
            };
        </script>
    ';
            $action .= '<div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="javascript:editData(rowData_'.md5($value->{"U_ID"}).')" class="dropdown-item">
                            Edit User
                            </a>
                        </li>
                        <li>
                            <a href="javascript:resetPassword(rowData_'.md5($value->{"U_ID"}).')" class="dropdown-item">
                            Reset Password
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item text-danger delete-action" data-id="'. $value->U_ID .'">
                                Delete User
                            </a>

                        </li>
                    </ul>
                </div>';


            $nestedData["Action"] = $action;
            $data[] = $nestedData;
        }

        $arrData = array(
            "draw"            => intval($_POST['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => isset($data) ? $data : []
        );

        return json_encode($arrData);
    }

    public function getUserByRole($roleId)
    {
       $data = DB::table('_users')
           ->join('_user_roles', '_users.UR_ID', '=', '_user_roles.UR_ID')
           ->where('UR_ID', $roleId)
           ->select([
               '_users.*'
           ])->get();
       return $data ?: null;
    }
}
