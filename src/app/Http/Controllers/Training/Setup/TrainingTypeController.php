<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Training\LTrainingType;
use App\Entities\Training\TrainingType;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingTypeController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.trainingtype.trainingtype', [
            'trainingtype' => null
        ]);
    }

    public function edit(Request $request, $id)
    {
        $trainingtype = LTrainingType::select('*')
            ->where('training_type_id', '=', $id)
            ->first();

        return view('training.setup.trainingtype.trainingtype', [
            'trainingtype' => $trainingtype
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LTrainingType::all();
        return datatables()->of($queryResult)
            ->addColumn('status_yn', function($query) {
                if($query->status_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('activation_start_date', function ($query) {
                return Carbon::parse($query->activation_start_date)->format('Y-m-d');
            })
            ->addColumn('activation_end_date', function ($query) {
                return Carbon::parse($query->activation_end_date)->format('Y-m-d');
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('training-type.training-type-edit', [$query->training_type_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {

        $response = $this->training_type_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-type.training-type-index');
    }

    public function update(Request $request, $id) {
        $response = $this->training_type_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-type.training-type-index');
    }

    private function training_type_api_ins(Request $request)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';

        try {
            $training_type_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TRAINING_TYPE_ID' => [
                    'value' => &$training_type_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_TRAINING_TYPE_NAME' => $postData['type_name'],
                'P_ACTIVATION_START_DATE' => $fromDate,
                'P_ACTIVATION_END_DATE' => $toDate,
                'P_STATUS_YN' => ($postData['requsition_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_TYPE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function training_type_api_upd($request, $id)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';

        try {
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TRAINING_TYPE_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_TRAINING_TYPE_NAME' => $postData['type_name'],
                'P_ACTIVATION_START_DATE' => $fromDate,
                'P_ACTIVATION_END_DATE' => $toDate,
                'P_STATUS_YN' => ($postData['requsition_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_TYPE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
