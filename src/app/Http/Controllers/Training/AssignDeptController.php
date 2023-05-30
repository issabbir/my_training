<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LDepartment;
use App\Entities\Admin\LGeoCountry;
use App\Entities\Training\AssignDepartmentDtl;
use App\Entities\Training\AssignDepartmentMst;
use App\Entities\Training\ForeignTour;
use App\Entities\Training\LTourSponsor;
use App\Entities\Training\LTourTypes;
use App\Entities\Training\TrainingCalenderDtl;
use App\Entities\Training\TrainingInfo;
use App\Entities\Training\TrainingNotification;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignDeptController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        return view('training.assigndept.assigndept', [
            'assigndept' => null,
            'assigndeptmst' => null,
            'department' => LDepartment::all(),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $assigndeptmst = AssignDepartmentMst::select('*')
            ->where('assignment_mst_id', '=', $id)
            ->first();//dd($assigndeptmst);

        $query = <<<QUERY
 SELECT LD.DEPARTMENT_NAME, EMP.EMP_NAME, ADDT.*
  FROM TIMS.ASSIGN_DEPARTMENT_MST  ADM,
       TIMS.ASSIGN_DEPARTMENT_DTL  ADDT,
       PMIS.L_DEPARTMENT           LD,
       PMIS.EMPLOYEE               EMP
 WHERE     ADM.ASSIGNMENT_MST_ID = ADDT.ASSIGNMENT_MST_ID
    AND LD.DEPARTMENT_ID = ADDT.ASSIGN_DEPT_ID
    AND EMP.EMP_ID = ADDT.ASSIGN_TO
    AND ADDT.ASSIGNMENT_MST_ID = :ASSIGNMENT_MST_ID
QUERY;

        $assigndeptdtl = DB::select($query, ['ASSIGNMENT_MST_ID' => $id]);


        $allDept = AssignDepartmentDtl::where('assignment_mst_id', $id)->get(['assign_dept_id'])->pluck('assign_dept_id')->toArray();
        $allDept_count = count($allDept);


        return view('training.assigndept.assigndept', [
            'assigndept' => $assigndeptdtl,
            'assigndeptmst' => $assigndeptmst,
            'department' => LDepartment::all(),
            'allDept' => json_encode($allDept),
            'allDept_count' => $allDept_count,
        ]);
    }

    public function dataTableList()
    {
        $query = "SELECT adm.assignment_mst_id, adm.training_id, tinfo.training_number, adm.assignment_date, tinfo.training_title
FROM TRAINING_INFO TINFO, ASSIGN_DEPARTMENT_MST ADM
WHERE ADM.TRAINING_ID = TINFO.TRAINING_ID";

        $queryResult = DB::select(DB::raw($query));


        return datatables()->of($queryResult)
            ->addColumn('assignment_date', function ($query) {
                return Carbon::parse($query->assignment_date)->format('Y-m-d');
            })
            ->addColumn('view_status', function ($query) {
                $baseUrl = request()->root();
                $html = <<<HTML
<a  target="_blank" href="{$baseUrl}/report/render/assign_dpt_status?xdo=/~weblogic/TIMS/RPT_ASSIGN_DEPT_STATUS.xdo&p_assignment_mst_id={$query->assignment_mst_id}&type=pdf&filename=assign_dpt_status" class="btn btn-dark btn-sm"> View Status</a>
HTML;
                return $html;
            })
            ->addColumn('action', function ($query) {
                return '<a href="' . route('assign-dept.assign-dept-edit', [$query->assignment_mst_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })->rawColumns(['assignment_date','view_status','action'])

            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
        $response = $this->assign_dept_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('assign-dept.assign-dept-index');
    }

    public function update(Request $request, $id)
    {
        //dd($request);
        $response = $this->assign_dept_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('assign-dept.assign-dept-index');
    }

    private function assign_dept_api_ins(Request $request)
    {
        $postData = $request->post();

        $assignment_date = isset($postData['assignment_date']) ? date('Y-m-d', strtotime($postData['assignment_date'])) : '';
        try {
            DB::beginTransaction();
            $assignment_mst_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_ASSIGNMENT_MST_ID' => [
                    'value' => &$assignment_mst_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_ASSIGNMENT_DATE' => $assignment_date,
                'P_TRAINING_ID' => $postData['training_id'],
                'P_ACTIVE_YN' => 'Y',
                'P_REFERENCE' => $postData['reference_no'],
                'P_REMARKS' => $postData['assign_dept_description'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_APPROVAL_PKG.ASSIGN_DEPARTMENT_MST_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            if ($request->get('training_id')) {
                    foreach ($request->get('dept_id') as $indx => $value) {
                        $assignment_dtl_id = null;
                        $status_code = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params_dtl = [
                            "P_ASSIGNMENT_DTL_ID" => [
                                'value' => &$assignment_dtl_id,
                                'type' => \PDO::PARAM_INPUT_OUTPUT,
                                'length' => 255
                            ],
                            "P_ASSIGNMENT_MST_ID" => $params['P_ASSIGNMENT_MST_ID']['value'],
                            "P_ASSIGN_DEPT_ID" => $request->get('dept_id')[$indx],
                            "P_ASSIGN_TO" => $request->get('dept_head_id')[$indx],
//                            "P_NOTE_TO_DEPARTMENT" => $request->get('note_to_dept')[$indx],
                            "P_DEPARTMENT_CAPACITY" => $request->get('department_capacity')[$indx],
                            "P_ACTIVE_YN" => 'Y',
                            "P_REMARKS" => $request->get('tab_remarks')[$indx],
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "o_status_message" => &$status_message
                        ];

                        //dd($params_dtl);
                        DB::executeProcedure("TIMS.TRAINING_APPROVAL_PKG.ASSIGN_DEPARTMENT_DTL_PR", $params_dtl);
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

    private function assign_dept_api_upd(Request $request, $id)
    {
        $postData = $request->post();

        $assignment_date = isset($postData['assignment_date']) ? date('Y-m-d', strtotime($postData['assignment_date'])) : '';
        try {
            DB::beginTransaction();

            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_ASSIGNMENT_MST_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_ASSIGNMENT_DATE' => $assignment_date,
                'P_TRAINING_ID' => $postData['training_id'],
                'P_ACTIVE_YN' => 'Y',
                'P_REFERENCE' => $postData['reference_no'],
                'P_REMARKS' => $postData['assign_dept_description'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_APPROVAL_PKG.ASSIGN_DEPARTMENT_MST_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            if ($request->get('dept_id')) {
                $assign_mst_id = $request->get('assignment_mst_id')[0];
                if ($request->get('assignment_mst_id')) {
                    AssignDepartmentDtl::where('assignment_mst_id', $assign_mst_id)->delete();
                    DB::commit();
                }
                foreach ($request->get('dept_id') as $indx => $value) {
                    $assignment_dtl_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_ASSIGNMENT_DTL_ID" => [
                            'value' => &$assignment_dtl_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        "P_ASSIGNMENT_MST_ID" => $params['P_ASSIGNMENT_MST_ID']['value'],
                        "P_ASSIGN_DEPT_ID" => $request->get('dept_id')[$indx],
                        "P_ASSIGN_TO" => $request->get('dept_head_id')[$indx],
//                      "P_NOTE_TO_DEPARTMENT" => $request->get('note_to_dept')[$indx],
                        "P_DEPARTMENT_CAPACITY" => $request->get('department_capacity')[$indx],
                        "P_ACTIVE_YN" => 'Y',
                        "P_REMARKS" => $request->get('tab_remarks')[$indx],
                        "P_INSERT_BY" => auth()->id(),
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];

                    DB::executeProcedure("TIMS.TRAINING_APPROVAL_PKG.ASSIGN_DEPARTMENT_DTL_PR", $params_dtl);
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
            $result = AssignDepartmentDtl::where('assignment_dtl_id', $request->get("assignment_dtl_id"))->delete();
            DB::commit();

            /*$notification_id = TrainingNotification::where('assignment_mst_id', $request->get("assignment_mst_id"))
                ->where('assign_to', $request->get("assign_to"))
                ->where('assign_dept_id', $request->get("dept_id"))
                ->get(['notification_id'])->pluck('notification_id')->first();

            $resultDtl = TrainingNotification::where('notification_id', $notification_id)->delete();*/

            $notificationResultDtl = TrainingNotification::where(
                [
                    ['assignment_mst_id', '=', $request->get("assignment_mst_id")],
                    ['assign_to', '=', $request->get("assign_to")],
                    ['assign_dept_id', '=', $request->get("dept_id")]
                ]
            )->delete();
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
    }

}
