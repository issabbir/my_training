<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Training\LTraineeType;
use App\Entities\Training\TrainingType;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraineeTypeController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.traineetype.traineetype', [
            'traineetype' => null
        ]);
    }

    public function edit(Request $request, $id)
    {
        $traineetype = LTraineeType::select('*')
            ->where('trainee_type_id', '=', $id)
            ->first();

        return view('training.setup.traineetype.traineetype', [
            'traineetype' => $traineetype
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LTraineeType::orderBy('trainee_type', 'ASC')->get();
        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('trainee-type.trainee-type-edit', [$query->trainee_type_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {

        $response = $this->trainee_type_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainee-type.trainee-type-index');
    }

    public function update(Request $request, $id) {
        $response = $this->trainee_type_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainee-type.trainee-type-index');
    }


    private function trainee_type_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $location_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TRAINEE_TYPE_ID' => [
                    'value' => &$location_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_TRAINEE_TYPE_NO' => '',
                'P_TRAINEE_TYPE' => $postData['trainee_type_name'],
                'P_TRAINEE_TYPE_BN' => $postData['trainee_type_name_bn'],
                'P_CPA_YN' => ($postData['trainee_type_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_ACTIVE_YN' => $postData['active_status'],
                'p_insert_by' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINEE_TYPE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

    private function trainee_type_api_upd($request, $id)
    {
        $postData = $request->post();

        try {
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TRAINEE_TYPE_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_TRAINEE_TYPE_NO' => '',
                'P_TRAINEE_TYPE' => $postData['trainee_type_name'],
                'P_TRAINEE_TYPE_BN' => $postData['trainee_type_name_bn'],
                'P_CPA_YN' => ($postData['trainee_type_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_ACTIVE_YN' => $postData['active_status'],
                'p_insert_by' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINEE_TYPE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

}
