<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Users;
use Illuminate\Support\Carbon;
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
            'U_IMAGE_PROFILE' => $data['U_IMAGE_PROFILE'],
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_TIME' => now(),
        ];

        $U_ID = DB::table('_users')->insertGetId($insertData);
        $hashedPassword = Hash::make($U_ID . $data['U_PASSWORD']);

        // Langkah 3: Update password hash
        DB::table('_users')->where('U_ID', $U_ID)->update([
            'U_PASSWORD_HASH' => $hashedPassword,
        ]);
        return $this->getById($U_ID);
    }

    public function update(array $data, $U_ID)
    {
        log::info('update user repo');
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
            $updateData['U_PASSWORD_HASH'] = Hash::make($U_ID.$data['U_PASSWORD']);
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
            $updateData['U_IMAGE_PROFILE'] = $data['U_IMAGE_PROFILE']; // Simpan langsung path yang diterima
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
            2 => 'ROLE_NAME',
            3 => 'U_EMAIL',
            4 => 'U_ADDRESS',
            5 => 'U_PHONE',
            6 => 'U_LOGIN_TIME',
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
                "_users.U_ID", "_users.U_NAME", "_users.UR_ID","_users.U_SEX","_users.U_LOGIN_TIME","_users.U_EMAIL","_users.U_ADDRESS","_users.U_PHONE","_users.U_IMAGE_PROFILE",'ur.ROLE_NAME'
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
            $nestedData["User Name"] = !empty($value->{"U_NAME"}) ? "<span style='opacity: 0.8'>".$value->{"U_NAME"}."</span>" : "<span style='opacity: 0.5; color: #000000;'>-</span>";
            $nestedData["User Email"] = !empty($value->{"U_EMAIL"}) ? $value->{"U_EMAIL"} : "<span style='opacity: 0.5; color: #000000;'>-</span>";
            $nestedData["User Address"] = !empty($value->{"U_ADDRESS"}) ? $value->{"U_ADDRESS"} : "<span style='opacity: 0.5; color: #000000;'>-</span>";
            $nestedData["User Phone"] = !empty($value->{"U_PHONE"})
                ? "<a href='https://wa.me/" . $value->{"U_PHONE"} . "' target='_blank' style='text-decoration: none; color: #25D366;'>"
                . $value->{"U_PHONE"} .
                "</a>"
                : "<span style='opacity: 0.5; color: #000000;'>-</span>";
            $nestedData["User Sex"] = !empty($value->{"U_SEX"}) ? "<span style='opacity: 0.8'>".$value->{"U_SEX"}."</span>" : "<span style='opacity: 0.5; color: #000000;'>-</span>";
            $nestedData["User Role"] = !empty($value->{"ROLE_NAME"}) ? $value->{"ROLE_NAME"} : "<span style='opacity: 0.5; color: #000000;'>-</span>";
            $nestedData["Last Login"] = !empty($value->{"U_LOGIN_TIME"})
                ? Carbon::parse($value->{"U_LOGIN_TIME"})->format('d M Y')
                : "<span style='opacity: 0.5; color: #888;'>-</span>";

            $action = "";
            $action .= '
        <script type="text/javascript">
            var rowData_'.md5($value->{"U_ID"}).' = {
                "U_ID" : "'.$value->{"U_ID"}.'",
                "U_NAME" : "'.$value->{"U_NAME"}.'",
                "U_SEX" : "'.$value->{"U_SEX"}.'",
                "U_EMAIL" : "'.$value->{"U_EMAIL"}.'",
                "U_ADDRESS" : "'.$value->{"U_ADDRESS"}.'",
                "U_IMAGE_PROFILE" : "'.$value->{"U_IMAGE_PROFILE"}.'",
                "U_PHONE" : "'.$value->{"U_PHONE"}.'",
                "ROLE_NAME" : "'.$value->{"ROLE_NAME"}.'",
                "UR_ID" : "'.$value->{"UR_ID"}.'",
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
