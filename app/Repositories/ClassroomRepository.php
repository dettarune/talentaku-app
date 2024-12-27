<?php

namespace App\Repositories;

use App\Models\t_classrooms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClassroomRepository implements ClassroomRepositoryInterface
{
    public function getAll()
    {
        $classrooms = t_classrooms::all()->select([
            'CLSRM_ID',
            'CLSRM_NAME',
            'CLSRM_TYPE',
            'CLSRM_GRADE',
        ]);
        return $classrooms;
    }

    public function getById($id)
    {
        $classroom = t_classrooms::find($id);
        if ($classroom) {
            $classroom->FINAL_CLSRM_NAME = $this->generateFinalClassroomName($classroom);
        }
        return $classroom;
    }

    public function create(array $data)
    {
        $insertData = [
            'CLSRM_NAME' => $data['CLSRM_NAME'],
            'CLSRM_TYPE' => $data['CLSRM_TYPE'],
            'CLSRM_GRADE' => $data['CLSRM_GRADE'],
            'CLSRM_DESCRIPTION' => $data['CLSRM_DESCRIPTION'],
            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
            'SYS_CREATE_AT' => now(),
        ];
        return t_classrooms::create($insertData);
    }

    public function update(array $data, $id)
    {
        $classroom = $this->getById($id);
        if ($classroom) {
            $updateData = [
                'SYS_UPDATE_USER' => $data['SYS_UPDATE_USER'] ?? 'System',
                'SYS_UPDATE_AT' => now(),
            ];
            foreach (['CLSRM_NAME', 'CLSRM_TYPE', 'CLSRM_GRADE', 'CLSRM_DESCRIPTION'] as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }
            $result = t_classrooms::where('CLSRM_ID',$id)->update($updateData);
            return $this->getById($result);
        }
        return null;
    }

    public function delete($id)
    {
        return t_classrooms::destroy($id);
    }

    private function generateFinalClassroomName($classroom)
    {
        switch ($classroom->CLSRM_TYPE) {
            case 'KB':
                return "Kelas {$classroom->CLSRM_GRADE} kelompok {$classroom->CLSRM_NAME}";
            case 'SD':
                return "Kelas {$classroom->CLSRM_GRADE}-{$classroom->CLSRM_NAME}";
            default:
                return $classroom->CLSRM_NAME;
        }
    }

    public function getDatatables()
    {
        $columns = array(
            0 => 'CLASSROOM_NAME',
            1 => 'CLSRM_TYPE',
            2 => 'CLSRM_GRADE',
            3 => 'CLSRM_DESCRIPTION',
        );

        $limit = $_POST['length'];
        $start = $_POST['start'];
        $orderColumnIndex = $_POST['order']['0']['column'] ?? null;
        $order = isset($columns[$orderColumnIndex]) && $columns[$orderColumnIndex] !== 'CLASSROOM_NAME'
            ? $columns[$orderColumnIndex]
            : 'CLSRM_NAME';

        $dir = $_POST['order']['0']['dir'] ?? 'asc';

        $baseData = t_classrooms::query()
            ->select([
                't_classrooms.*',
                't_classrooms.CLSRM_TYPE',
                't_classrooms.CLSRM_GRADE',
                't_classrooms.CLSRM_DESCRIPTION',
            ]);

        $baseCount = clone $baseData;
        $totalData = $baseCount->count();
        $totalFiltered = $totalData;

        // Jika tidak ada pencarian
        if (empty($_POST['search']['value'])) {
            $baseData->orderBy($order, $dir);
            $baseData->limit($limit);
            $baseData->offset($start);

            $dtData = $baseData->get()->transform(function ($classroom) {
                $classroom->CLASSROOM_NAME = $this->generateFinalClassroomName($classroom);
                return $classroom;
            });
        } else {
            $search = $_POST['search']['value'];
            $baseData->where("CLSRM_NAME", "like", "%".$search."%")
            ->orWhere("CLSRM_DESCRIPTION", "like", "%".$search."%");

            $filterCount = clone $baseData;
            $totalFiltered = $filterCount->count();

            $baseData->orderBy($order, $dir);
            $baseData->limit($limit);
            $baseData->offset($start);

            $dtData = $baseData->get()->transform(function ($classroom) {
                $classroom->CLASSROOM_NAME = $this->generateFinalClassroomName($classroom);
                return $classroom;
            });

            $search = strtolower($search);
            $dtData = $dtData->filter(function ($classroom) use ($search) {
                return strpos(strtolower($classroom->CLASSROOM_NAME), $search) !== false;
            });
        }

        foreach ($dtData as $value) {
            $nestedData["CLSRM_NAME"] = $value->{"CLSRM_NAME"};
            $nestedData["CLASSROOM_NAME"] = "<span style='opacity: 0.8'>".$value->{"CLASSROOM_NAME"}."</span>";
            $nestedData["CLSRM_TYPE"] = "<span style='opacity: 0.8'>".$value->{"CLSRM_TYPE"}."</span>";
            $nestedData["CLSRM_GRADE"] = $value->{"CLSRM_GRADE"};
            $nestedData["CLSRM_DESCRIPTION"] = $value->{"CLSRM_DESCRIPTION"};

            $action = '';
            $action .= '
            <script type="text/javascript">
                var rowData_'.md5($value->{"CLSRM_ID"}).' = {
                    "CLSRM_ID" : "'.$value->{"CLSRM_ID"}.'",
                    "CLSRM_NAME" : "'.$value->{"CLSRM_NAME"}.'",
                    "CLASSROOM_NAME" : "'.$value->{"CLASSROOM_NAME"}.'",
                    "CLSRM_TYPE" : "'.$value->{"CLSRM_TYPE"}.'",
                    "CLSRM_GRADE" : "'.$value->{"CLSRM_GRADE"}.'",
                    "CLSRM_DESCRIPTION" : "'.$value->{"CLSRM_DESCRIPTION"}.'",
                };
            </script>
        ';
            $action .= '<div class="dropdown">
                <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                    Action
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="javascript:editData(rowData_'.md5($value->{"CLSRM_ID"}).')" class="dropdown-item">
                        Edit
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="dropdown-item text-danger delete-action" data-id="'. $value->CLSRM_ID .'">
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

}
