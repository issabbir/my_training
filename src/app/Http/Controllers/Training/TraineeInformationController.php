<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\Trainee;
use App\Entities\Training\TrainingInfo;
use App\Entities\Training\TrainingScheduleMaster;
use App\Entities\Training\TrainingType;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraineeInformationController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        $traininginfo = TrainingInfo::select('training_info.training_title AS training_title',
            'training_info.training_id AS training_id')
            ->leftJoin('training_schedule_mst', 'training_schedule_mst.training_id', '=', 'training_info.training_id')
            ->orderBy('training_info.training_title', 'asc')
            ->distinct()
            ->where('training_schedule_mst.schedule_status_id','4')
            ->get();


        return view('training.traineeinfo.traineeinfo', [
            'traineeinfo' => null,
            'traininginfo' => $traininginfo
        ]);
    }

    public function edit(Request $request, $id)
    {
        $traineeinfo = Trainee::where('trainee_id', '=', $id)->get();
        $traininginfo = TrainingInfo::select('training_info.training_title AS training_title',
            'training_info.training_id AS training_id')
            ->leftJoin('training_schedule_mst', 'training_schedule_mst.training_id', '=', 'training_info.training_id')
            ->orderBy('training_info.training_title', 'asc')
            ->distinct()
            ->where('training_schedule_mst.schedule_status_id','4')
            ->get();

        return view('training.traineeinfo.traineeinfo', [
            'traineeinfo' => $traineeinfo,
            'traininginfo' => $traininginfo
        ]);
    }

    public function dataTableList()
    {
        $queryResult = Trainee::orderBy('insert_date', 'desc')->get();
        return datatables()->of($queryResult)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('trainee-information.trainee-information-edit', [$query->trainee_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {

        $response = $this->trainee_info_api_ins($request);

        $message = $response['o_status_message'];

        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainee-information.trainee-information-index');
    }

    public function update(Request $request, $id)
    {
        $response = $this->trainee_info_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainee-information.trainee-information-index');
    }

    private function trainee_info_api_ins(Request $request)
    {
        try {
            $trainee_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");


            $params = [
                'P_CPA_TRAINEE_YN' => 'N',
                'P_TRAINEE_ID' => $trainee_id,
                'P_TRAINEE_NAME' => $request->get("emp_name"),
                'P_TRAINEE_EMP_ID' => '',
                'P_DESIGNATION_ID' => '',
                'P_DEPT_ID' => '',
                'P_DESIGNATION_NAME' => $request->get("emp_designation"),
                'P_DEPARTMENT_NAME' => $request->get("emp_department"),
                'P_ORG_NAME' => $request->get("organization"),
                'P_Contact_Address' => $request->get("trainee_address"),
                'P_Cell_Number' => $request->get("contact_no"),
                'P_Emergency_Cell_Number' => $request->get("emrg_contact_no"),
                'P_Email' => $request->get("email_add"),
                'P_Previous_Training' => $request->get("prev_training_id"),
                'P_PAID_FEE_YN' => null,
                'P_COURSE_FEE' => null,
                'P_PAID_AMT' => null,
                'P_DISCOUNT_AMT' => null,
                'P_BANK_NAME' => $request->get("bank_name"),
                'P_BRANCH_NAME' => $request->get("branch_name"),
                'P_ACC_NO' => $request->get("acc_no"),
                'P_REMARKS' => $request->get("remarks"),
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_INFO_PR', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

    private function trainee_info_api_upd($request, $id)
    {
        try {
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_CPA_TRAINEE_YN' => 'N',
                'P_TRAINEE_ID' => $id,
                'P_TRAINEE_NAME' => $request->get("emp_name"),
                'P_TRAINEE_EMP_ID' => '',
                'P_DESIGNATION_ID' => '',
                'P_DEPT_ID' => '',
                'P_DESIGNATION_NAME' => $request->get("emp_designation"),
                'P_DEPARTMENT_NAME' => $request->get("emp_department"),
                'P_ORG_NAME' => $request->get("organization"),
                'P_Contact_Address' => $request->get("trainee_address"),
                'P_Cell_Number' => $request->get("contact_no"),
                'P_Emergency_Cell_Number' => $request->get("emrg_contact_no"),
                'P_Email' => $request->get("email_add"),
                'P_Previous_Training' => $request->get("prev_training_id"),
                'P_PAID_FEE_YN' => null,
                'P_COURSE_FEE' => null,
                'P_PAID_AMT' => null,
                'P_DISCOUNT_AMT' => null,
                'P_BANK_NAME' => $request->get("bank_name"),
                'P_BRANCH_NAME' => $request->get("branch_name"),
                'P_ACC_NO' => $request->get("acc_no"),
                'P_REMARKS' => $request->get("remarks"),
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_INFO_PR', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    public function scheduleData(Request $request)
    {
        $schedule_id = $request->get("schedule_id");
        $scheduleData = TrainingScheduleMaster::where('schedule_id', '=', $schedule_id)->get();
        return $scheduleData;
    }

}
