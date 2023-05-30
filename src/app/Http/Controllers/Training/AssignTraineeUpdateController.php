<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LDepartment;
use App\Entities\Admin\LGeoCountry;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Training\AssignDepartmentMst;
use App\Entities\Training\ForeignTour;
use App\Entities\Training\LTourSponsor;
use App\Entities\Training\LTourTypes;
use App\Entities\Training\NominatedTraineeDetail;
use App\Entities\Training\NominatedTraineeDtl;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignTraineeUpdateController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        return view('training.assigntraineeupdate.assigntraineeupdate');
    }

    public function dataTableList()
    {
        $role_key = json_decode(Auth::user()->roles->pluck('role_key'));
        if (in_array("TRAINING_ENTRY", $role_key)) {
            $query = <<<QUERY
select ntm.nominated_trainee_id as nominated_trainee_mst_id,
       ntm.assignment_mst_id,
       ntm.training_id,
       addt.assign_dept_id,
       (ti.training_title || ' (' || ti.training_number || ')') as training_name,
       to_char (adm.assignment_date, 'YYYY-MM-DD') as assignment_date,
       to_char (ntm.trainee_assign_date, 'YYYY-MM-DD') as trainee_assign_date,
       ntm.remarks
  from tims.nominated_trainee_mst ntm, tims.training_info ti, tims.assign_department_mst adm ,tims.assign_department_dtl addt
 where ntm.training_id = ti.training_id
    and adm.assignment_mst_id = ntm.assignment_mst_id
    and adm.assignment_mst_id = addt.assignment_mst_id
    and adm.training_id = ntm.training_id
    and addt.assign_dept_id = ntm.assign_dept_id
    and addt.assign_to = ntm.assign_to
QUERY;

            $queryResult = DB::select($query);
        }else{
            $userId = auth()->user()->emp_id;
            $query = <<<QUERY
select ntm.nominated_trainee_id as nominated_trainee_mst_id,
       ntm.assignment_mst_id,
       ntm.training_id,
       addt.assign_dept_id,
       (ti.training_title || ' (' || ti.training_number || ')') as training_name,
       to_char (adm.assignment_date, 'YYYY-MM-DD') as assignment_date,
       to_char (ntm.trainee_assign_date, 'YYYY-MM-DD') as trainee_assign_date,
       ntm.remarks
  from tims.nominated_trainee_mst ntm, tims.training_info ti, tims.assign_department_mst adm ,tims.assign_department_dtl addt
 where ntm.training_id = ti.training_id
    and adm.assignment_mst_id = ntm.assignment_mst_id
    and adm.assignment_mst_id = addt.assignment_mst_id
    and adm.training_id = ntm.training_id
    and addt.assign_dept_id = ntm.assign_dept_id
    and addt.assign_to = ntm.assign_to
    and addt.assign_to = :p_assign_to
QUERY;

            $queryResult = DB::select($query, ['p_assign_to' => $userId]);
        }


        return datatables()->of($queryResult)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('assign-trainee-update.assign-trainee-update-edit', [$query->nominated_trainee_mst_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function edit(Request $request, $id)
    {
        $querys = "select ntm.nominated_trainee_id as nominated_trainee_mst_id,
       ntm.assignment_mst_id,
       ntm.training_id,
       (ti.training_title || ' (' || ti.training_number || ')') as training_name,
       ti.coordination_name as coordination_name,
       ti.coordination_email as coordination_email,
       ti.coordination_cell as coordination_cell,
       to_char (adm.assignment_date, 'YYYY-MM-DD') as dept_assignment_date,
       to_char (ntm.trainee_assign_date, 'YYYY-MM-DD') as trainee_assign_date,
       ntm.remarks
  from tims.nominated_trainee_mst ntm, tims.training_info ti, tims.assign_department_mst adm
 where ntm.training_id = ti.training_id
 and adm.assignment_mst_id = ntm.assignment_mst_id
 and ntm.nominated_trainee_id = $id";

        $mstData = DB::selectOne(DB::raw($querys));

//        dd($mstData->training_id);

        $querys = "SELECT NTM.NOMINATED_TRAINEE_ID,
       NTD.NOMINATED_DTL_ID,
       NTM.ASSIGNMENT_MST_ID,
       NTM.TRAINING_ID,
       NTD.TRAINEE_ID,
       NTD.DEPARTMENT_ID,
       PEM.EMP_NAME,
       LDPT.DEPARTMENT_NAME,
       LDES.DESIGNATION,
       NTD.REMARKS
  FROM TIMS.NOMINATED_TRAINEE_MST  NTM,
       TIMS.NOMINATED_TRAINEE_DTL  NTD,
       PMIS.EMPLOYEE               PEM,
       PMIS.L_DEPARTMENT           LDPT,
       PMIS.L_DESIGNATION          LDES
 WHERE     NTM.NOMINATED_TRAINEE_ID = NTD.NOMINATED_TRAINEE_ID
       AND NTM.ASSIGNMENT_MST_ID = NTD.ASSIGNMENT_MST_ID
       AND PEM.EMP_ID = NTD.TRAINEE_ID
       AND LDPT.DEPARTMENT_ID = NTD.DEPARTMENT_ID
       AND LDES.DESIGNATION_ID = PEM.DESIGNATION_ID
       AND NTD.NOMINATED_TRAINEE_ID = $id";

        $dtlData = DB::select(DB::raw($querys));

//        dd($dtlData);

        $allTrainee = NominatedTraineeDetail::where('NOMINATED_TRAINEE_ID', $id)->get(['trainee_id'])->pluck('trainee_id')->toArray();
        $allTrainee_count = count($allTrainee);



        $emp = Auth::user()->employee;
        $tid = $mstData->training_id;
        $did = $emp->dpt_department_id;

        $sql = 'SELECT td.DEPARTMENT_CAPACITY
  FROM ASSIGN_DEPARTMENT_MST tm, ASSIGN_DEPARTMENT_DTL td
 WHERE     tm.ASSIGNMENT_MST_ID = td.ASSIGNMENT_MST_ID
       AND tm.training_id = :tid
       AND td.ASSIGN_DEPT_ID = :did
       AND td.DEPARTMENT_CAPACITY IS NOT NULL
       AND ROWNUM = 1';

        $data = DB::selectOne($sql, ['did' => $did, 'tid' => $tid]);

        //Existing count query
        $csql = 'SELECT COUNT (TRAINEE_ID) assign_trainee
                FROM NOMINATED_TRAINEE_DTL
                WHERE TRAINING_ID = :tid AND DEPARTMENT_ID = :did';

        $cdata = DB::selectOne($csql, ['did' => $did, 'tid' => $tid]);

        if(isset($data->department_capacity) && $data->department_capacity > $cdata->assign_trainee){
            $r_capacity = $data->department_capacity - $cdata->assign_trainee;
            $capacity = $data->department_capacity;
        }else {
            $r_capacity = 0;
            $capacity = $data->department_capacity;
        }

        return view('training.assigntraineeupdate.assigntraineeupdatedtl', [
            'dtlData' => $dtlData,
            'mstData' => $mstData,
            'allTrainee' => json_encode($allTrainee),
            'allTrainee_count' => $allTrainee_count,
            'capacity' => $capacity,
            'r_capacity' => $r_capacity,
        ]);
    }

    public function update(Request $request, $id)
    {
        $response = $this->assign_trainee_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('assign-trainee-update.assign-trainee-update-index');
    }

    private function assign_trainee_api_upd(Request $request,$id)
    {
//        dd($request);
        $postData = $request->post();
        $userId = auth()->user()->emp_id;
        $userInfo= Employee::where ('emp_id', '=', $userId)->first();
        try {
            DB::beginTransaction();
            $nominated_trainee_id = $id;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

//            dd($nominated_trainee_id);

            $params = [
                'P_NOMINATED_TRAINEE_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_ASSIGNMENT_MST_ID' => $postData['assignment_mst_id'],
                'P_TRAINING_ID' => $postData['training_id'],
                'P_ASSIGN_DEPT_ID' => $userInfo->dpt_department_id,
                'P_ASSIGN_TO' => $userId,
                'P_REMARKS' => $postData['master_remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

//            dd($params);

            DB::executeProcedure('TIMS.TRAINING_APPROVAL_PKG.NOMINATED_TRAINEE_MST_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

//            dd($request->get('department_id'));

            if ($request->get('department_id')) {
                if ($request->get('nominated_dtl_id')) {
                    NominatedTraineeDtl::where('nominated_trainee_id', $nominated_trainee_id)->delete();
                    DB::commit();
                }

                foreach ($request->get('trainee_id') as $indx => $value) {
                    $assignment_dtl_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_NOMINATED_DTL_ID" => [
                            'value' => &$assignment_dtl_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        "P_NOMINATED_TRAINEE_ID" => $params['P_NOMINATED_TRAINEE_ID']['value'],
                        "P_TRAINEE_ID" => $request->get('trainee_id')[$indx],
                        "P_DEPARTMENT_ID" => $request->get('department_id')[$indx],
                        "P_ASSIGNMENT_MST_ID" => $postData['assignment_mst_id'],
                        "P_REMARKS" => $request->get('tab_remarks')[$indx],
                        "P_INSERT_BY" => auth()->id(),
                        //"P_STATUS" => '',
                        "P_TRAINING_ID" => $postData['training_id'],
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];

                    DB::executeProcedure("TIMS.TRAINING_APPROVAL_PKG.NOMINATED_TRAINEE_DTL_PR", $params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        DB::commit();
        return $params;
    }

    public function removeDetailData(Request $request)
    {
        try {
            $querys = "DELETE FROM NOMINATED_TRAINEE_DTL WHERE NOMINATED_DTL_ID = '" . $request->get("nominated_dtl_id") . "'";
            $result = DB::select(DB::raw($querys));
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
    }
}
