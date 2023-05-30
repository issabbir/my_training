<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\LCourse;
use App\Entities\Training\LTraineeType;
use App\Entities\Training\LTrainingLocation;
use App\Entities\Training\LTrainingType;
use App\Entities\Training\Requisition;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequisitionController extends Controller
{
    use HasPermission;

    protected $employee;

    protected $auth;

    public function __construct(Employee $employee, Guard $auth)
    {
        $this->employee = $employee;
        $this->auth = $auth;
    }

    public function index(Request $request)
    {


        return view('training.requisition.requisition', [
            'requisition' => null,
            'traineeType' => LTraineeType::all(),
            'trainingType' => LTrainingType::where('STATUS_YN', 'Y')->get(),
            'lCourse' => LCourse::all(),
            'lTrainingLocation' => LTrainingLocation::all(),
            'loggedUser' => Auth::user(),
//            'myInfo' => Employee::where('emp_id',Auth::user()->emp_id)->first(['emp_name', 'emp_id']),
            'myInfo' => Employee::where('emp_id',Auth::user()->emp_id)->first(['emp_name', 'emp_id', 'designation_id', 'dpt_department_id']),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $requisition = Requisition::find($id);
        $whom_ids = explode(", ", $requisition->trainee_type_id);

        return view('training.requisition.requisition', [
            'requisition' => $requisition,
            'traineeType' => LTraineeType::all(),
            'trainingType' => LTrainingType::all(),
            'lCourse' => LCourse::all(),
            'lTrainingLocation' => LTrainingLocation::all(),
            'loggedUser' => Auth::user(),
            'whom_ids' => $whom_ids,
        ]);
    }

    public function dataTableList()
    {
        $querys = "SELECT TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_REQUISITION_GRID_LIST FROM DUAL";
        $queryResult = DB::select($querys);
        return datatables()->of($queryResult)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('training-requisition.training-requisition-edit', [$query->traning_req_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
//        dd($request);
        $response = $this->training_requisition_api_ins($request);

        $message = $response['o_status_message'];

        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-requisition.training-requisition-index');
    }

    public function update(Request $request, $id)
    {
        $response = $this->training_requisition_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-requisition.training-requisition-index');
    }

    private function training_requisition_api_ins(Request $request)
    {
        $postData = $request->post();

        $trainee_type = implode(', ', $postData['for_whom']);

        $emp_id = isset($postData['emp_id']) ? ($postData['emp_id']) : '';
        $req_date = isset($postData['req_date']) ? date('Y-m-d', strtotime($postData['req_date'])) : '';

        $requsition_status = 'N';

        try {
            $req_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [

                'P_REQ_ID' => [
                    'value' => &$req_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_REQ_DATE' => $req_date,
                'P_EMP_ID' => $emp_id,
                'P_EMP_NAME' => $postData['emp_name'],
                'P_DESIGNATION_ID' => $postData['designation_id'],
                'P_DEPT_ID' => $postData['department_id'],
                'P_COURSE_ID' => null, //$postData['course_id'],
                'P_COURSE' => $postData['course_name'],
                'P_CPA_YN' => 'Y',
//                'P_REQ_STATUS' => $postData['requsition_status'],
                'P_REQ_STATUS' => $requsition_status,
                'P_T_LOCATION' => null,
                'P_OBJECTIVES' => $postData['objectives'],
                'P_TRAINEE_T_ID' => $trainee_type,
                'P_T_TYPE_ID' => $postData['training_category'],
                'P_LOCATION_ID' => $postData['training_location_id'],
                'P_REMARKS' => $postData['remarks'],
                'p_insert_by' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_REQUISITION_PR", $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function training_requisition_api_upd($request, $id)
    {
        $postData = $request->post();

        $trainee_type = implode(', ', $postData['for_whom']);

        if(array_key_exists('approve',$postData)){
            $requsition_status = 'Y';
        }
        else{
            $requsition_status = 'N';
        }

        $emp_id = isset($postData['emp_id']) ? ($postData['emp_id']) : '';
        $req_date = isset($postData['req_date']) ? date('Y-m-d', strtotime($postData['req_date'])) : '';

        try {
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [

                'P_REQ_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_REQ_DATE' => $req_date,
                'P_EMP_ID' => $emp_id,
                'P_EMP_NAME' => $postData['emp_name'],
                'P_DESIGNATION_ID' => $postData['designation_id'],
                'P_DEPT_ID' => $postData['department_id'],
                'P_COURSE_ID' => null,
                'P_COURSE' => $postData['course_name'],
//                'P_CPA_YN' => $postData['cpa_yn'],
//                'P_REQ_STATUS' => $postData['requsition_status'],
                'P_CPA_YN' => 'Y',
                'P_REQ_STATUS' => $requsition_status,
                'P_T_LOCATION' => null,
                'P_OBJECTIVES' => $postData['objectives'],
                'P_TRAINEE_T_ID' => $trainee_type,
                'P_T_TYPE_ID' => $postData['training_category'],
                'P_LOCATION_ID' => $postData['training_location_id'],
                'P_REMARKS' => $postData['remarks'],
                'p_insert_by' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_REQUISITION_PR", $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }
}
