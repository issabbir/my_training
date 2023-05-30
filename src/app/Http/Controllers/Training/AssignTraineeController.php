<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LDepartment;
use App\Entities\Admin\LGeoCountry;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Training\AssignDepartmentMst;
use App\Entities\Training\ForeignTour;
use App\Entities\Training\LTourSponsor;
use App\Entities\Training\LTourTypes;
use App\Entities\Training\NominatedTraineeMaster;
use App\Entities\Training\Trainee;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AssignTraineeController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        return view('training.assigntrainee.assigntrainee');
    }

    public function dataTableList()
    {
        $userId = auth()->user()->emp_id;
        $role_key = json_decode(Auth::user()->roles->pluck('role_key'));

        if (in_array("TRAINING_ENTRY", $role_key)) {//dd('TRAINING_ENTRY');
            $query = <<<QUERY
SELECT adm.assignment_mst_id,
       addt.assign_dept_id,
       to_char (adm.assignment_date, 'YYYY-MM-DD') as assignment_date,
       (ti.training_title || ' (' || ti.training_number || ')') as training_name,
       ti.training_number,
       adm.remarks,
       addt.assign_dept_id,
       addt.assign_to,
       LD.DEPARTMENT_NAME,
       tln.notification_step_name
  FROM tims.assign_department_mst adm, tims.assign_department_dtl addt ,tims.training_info ti, TIMS.TRAINING_NOTIFICATION TTN, TIMS.L_NOTIFCATION TLN, PMIS.L_DEPARTMENT LD
 WHERE ti.training_id = adm.training_id
 AND adm.assignment_mst_id = addt.assignment_mst_id
 and adm.assignment_mst_id = ttn.assignment_mst_id
 and addt.assign_dept_id = ttn.assign_dept_id
 and addt.assign_to = ttn.assign_to
 and addt.assign_dept_id = LD.DEPARTMENT_ID
 and tln.notification_step_id = ttn.notification_step_id
QUERY;

            $queryResult = DB::select($query);
        } else {//dd('OTHER');
            $query = <<<QUERY
SELECT adm.assignment_mst_id,
       addt.assign_dept_id,
       to_char (adm.assignment_date, 'YYYY-MM-DD') as assignment_date,
       (ti.training_title || ' (' || ti.training_number || ')') as training_name,
       ti.training_number,
       adm.remarks,
       addt.assign_dept_id,
       addt.assign_to,
       LD.DEPARTMENT_NAME,
       tln.notification_step_name
  FROM tims.assign_department_mst adm, tims.assign_department_dtl addt ,tims.training_info ti, TIMS.TRAINING_NOTIFICATION TTN, TIMS.L_NOTIFCATION TLN, PMIS.L_DEPARTMENT LD
 WHERE ti.training_id = adm.training_id
 AND adm.assignment_mst_id = addt.assignment_mst_id
 and adm.assignment_mst_id = ttn.assignment_mst_id
 and addt.assign_dept_id = ttn.assign_dept_id
 and addt.assign_to = ttn.assign_to
 and addt.assign_dept_id = LD.DEPARTMENT_ID
 and tln.notification_step_id = ttn.notification_step_id
 and addt.assign_to = :p_assign_to
QUERY;

            $queryResult = DB::select($query, ['p_assign_to' => $userId]);
        }


        return datatables()->of($queryResult)
            ->addColumn('action', function ($data) {
                //$querys = "SELECT * FROM NOMINATED_TRAINEE_MST WHERE ASSIGNMENT_MST_ID = $data->assignment_mst_id AND ASSIGN_DEPT_ID = $data->assign_dept_id AND ASSIGN_TO =  $data->assign_to";
                //$queryResult = DB::select(DB::raw($querys));
                //$nomTraineeInfo =

                $nomTraineeInfo = NominatedTraineeMaster::where(
                    [
                        ['assignment_mst_id', '=', $data->assignment_mst_id],
                        ['assign_dept_id', '=', $data->assign_dept_id],
                        ['assign_to', '=', $data->assign_to]
                    ]
                )->count();
                //dd($nomTraineeInfo);

                if (empty($nomTraineeInfo)) {
                    return '<a class="btn btn-outline-primary btn-sm tooltip-light" data-toggle="tooltip" data-placement="top" title="Assign Trainee" data-original-title="Tooltip on top" href="' . route('assign-trainee.assign-trainee-edit', [$data->assignment_mst_id]) . '"><i class="bx bx-send cursor-pointer"></i></a>';
                } else {
                    return 'Trainee Assigned';
                }
            })
            ->addColumn('department_name', function ($data) {
                //$userId = auth()->user()->emp_id;
                $query = <<<QUERY
select LD.DEPARTMENT_NAME from PMIS.L_DEPARTMENT LD
 left join tims.assign_department_dtl addt on addt.ASSIGN_DEPT_ID = LD.DEPARTMENT_ID
 where addt.ASSIGNMENT_MST_ID = :mst_id
QUERY;

                $department = DB::select($query, ['mst_id' => $data->assignment_mst_id]);
                return implode(', ', array_column($department, 'department_name'));
            })
            ->addColumn('notification_step_name', function ($data) {
                return '<div class="badge badge-pill badge-light-info">' . $data->notification_step_name . '</div>';
            })
            ->rawColumns(['notification_step_name', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function edit(Request $request, $id)
    {
        $query = <<<QUERY
SELECT ADM.ASSIGNMENT_MST_ID,
       ADM.TRAINING_ID,
       TO_CHAR (ADM.ASSIGNMENT_DATE, 'YYYY-MM-DD') AS ASSIGNMENT_DATE,
       (TI.TRAINING_TITLE || ' (' || TI.TRAINING_NUMBER || ')') AS TRAINING_NAME,
       TI.COORDINATION_NAME,
       TI.COORDINATION_CELL,
       TI.COORDINATION_EMAIL,
       ADM.REMARKS
  FROM TIMS.ASSIGN_DEPARTMENT_MST ADM, TIMS.TRAINING_INFO TI
 WHERE TI.TRAINING_ID = ADM.TRAINING_ID
 AND ASSIGNMENT_MST_ID = :ASSIGNMENT_MST_ID
QUERY;

        $assigntraineetmst = DB::select($query, ['ASSIGNMENT_MST_ID' => $id]);

//        dd($assigntraineetmst);


        /*$querys = "SELECT NTM.NOMINATED_TRAINEE_ID,
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
       AND NTM.ASSIGNMENT_MST_ID = $id";

        $assigndeptdtl = DB::select(DB::raw($querys));*/


        $emp = Auth::user()->employee;
        $tid = $assigntraineetmst[0]->training_id;
        $did = $emp->dpt_department_id;//dd($did.'-'.$tid);

        /*$sql = 'SELECT td.DEPARTMENT_CAPACITY
  FROM ASSIGN_DEPARTMENT_MST tm, ASSIGN_DEPARTMENT_DTL td
 WHERE     tm.ASSIGNMENT_MST_ID = td.ASSIGNMENT_MST_ID
       AND tm.training_id = :tid
       AND td.ASSIGN_DEPT_ID = :did
       AND td.DEPARTMENT_CAPACITY IS NOT NULL
       AND ROWNUM = 1';*/
        $sql = 'SELECT SUM (DEPARTMENT_CAPACITY)     DEPARTMENT_CAPACITY
  FROM ASSIGN_DEPARTMENT_DTL
 WHERE ASSIGNMENT_MST_ID = :ASSIGNMENT_MST_ID';

        $data = DB::selectOne($sql, ['ASSIGNMENT_MST_ID' => $id]);
        //$data = DB::selectOne($sql, ['ASSIGNMENT_MST_ID' => $did, 'tid' => $tid]);

        //Existing count query
        $csql = ' SELECT COUNT (TRAINEE_ID) assign_trainee
                FROM NOMINATED_TRAINEE_DTL
                WHERE TRAINING_ID = :tid AND DEPARTMENT_ID = :did';

        $cdata = DB::selectOne($csql, ['did' => $did, 'tid' => $tid]);
//dd($data);
        if (isset($data->department_capacity) && $data->department_capacity > $cdata->assign_trainee) {
            $capacity = $data->department_capacity - $cdata->assign_trainee;
        } else {
            $capacity = 0;
        }

//        dd($capacity);

        $query = <<<QUERY
select LD.DEPARTMENT_ID, LD.DEPARTMENT_NAME, addt.DEPARTMENT_CAPACITY from PMIS.L_DEPARTMENT LD
 left join tims.assign_department_dtl addt on addt.ASSIGN_DEPT_ID = LD.DEPARTMENT_ID
 where addt.ASSIGNMENT_MST_ID = :mst_id
QUERY;

        $department = DB::select($query, ['mst_id' => $id]);

        return view('training.assigntrainee.assigntraineedtl', [
            //'assigndept' => $assigndeptdtl,
            'assigntraineetmst' => $assigntraineetmst,
            'capacity' => $capacity,
            'department' => $department,
        ]);
    }

    /*public function post(Request $request)
    {
        $response = $this->assign_trainee_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('assign-trainee.assign-trainee-index');
    }*/

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

        return redirect()->route('assign-trainee.assign-trainee-index');
    }

    private function assign_trainee_api_upd(Request $request)
    {

        $postData = $request->post();
        $userId = auth()->user()->emp_id;
        $userInfo = Employee::where('emp_id', '=', $userId)->first();

        try {
            DB::beginTransaction();
            $nominated_trainee_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_NOMINATED_TRAINEE_ID' => [
                    'value' => &$nominated_trainee_id,
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
            DB::executeProcedure('TIMS.TRAINING_APPROVAL_PKG.NOMINATED_TRAINEE_MST_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }


            if ($request->get('training_id')) {

//                $emp = Auth::user()->employee;
////                dd($emp);
//                $tid = $postData['training_id'];
////                dd($tid);
//                $did = $emp->dpt_department_id;
////                dd($did);
//
//                $sql = 'SELECT td.DEPARTMENT_CAPACITY
//  FROM ASSIGN_DEPARTMENT_MST tm, ASSIGN_DEPARTMENT_DTL td
// WHERE     tm.ASSIGNMENT_MST_ID = td.ASSIGNMENT_MST_ID
//       AND tm.training_id = :tid
//       AND td.ASSIGN_DEPT_ID = :did
//       AND td.DEPARTMENT_CAPACITY IS NOT NULL
//       AND ROWNUM = 1';

//                dd($sql);

//                $data = DB::selectOne($sql, ['did' => $did, 'tid' => $tid]);

//                print_r($data->department_capacity);
//                dd($data->department_capacity);


                //Existing count query
//                $csql = ' SELECT COUNT (TRAINEE_ID) assign_trainee
//                FROM NOMINATED_TRAINEE_DTL
//                WHERE TRAINING_ID = :tid AND DEPARTMENT_ID = :did';
//
//                $cdata = DB::selectOne($csql, ['did' => $did, 'tid' => $tid]);

//                dd($cdata->assign_trainee);

//                $a_data = $data->department_capacity - $cdata;

//                if($data->department_capacity > $cdata->assign_trainee){
//                    $a_data = $data->department_capacity - $cdata->assign_trainee;
//                }else {
//                    return view('training.assigntrainee.assigntraineedtl')->with('msg', "Department capacity is over");
//                }

//                $trainees = count($request->get('trainee_id'));

//                dd($trainees);

//                if ($trainees > $a_data) {
//
//                    return view('training.assigntrainee.assigntraineedtl')->with('msg', "Department capacity is over");
//
//                } else{
//                    dd($request);
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
                        "P_TRAINING_ID" => $postData['training_id'],
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];
                    DB::executeProcedure("TIMS.TRAINING_APPROVAL_PKG.NOMINATED_TRAINEE_DTL_PR", $params_dtl);//dd($params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }
                }
//                }


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

    public function chkDepCapacity(Request $request)
    {
        try {
            $query = <<<QUERY
 select addt.department_capacity from tims.assign_department_dtl addt
 where addt.assignment_mst_id = :mst_id
 and addt.assign_dept_id = :dept_id
QUERY;

            $capacity = DB::selectOne($query, ['mst_id' => $request->get("assignment_mst_id"), 'dept_id' => $request->get("department_id")]);

            return $capacity->department_capacity;
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
    }
}
