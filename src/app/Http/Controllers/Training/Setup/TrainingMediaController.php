<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Training\LTrainingMedia;
use App\Entities\Training\TrainingType;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingMediaController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.trainingmedia.trainingmedia', [
            'trainingmedia' => null
        ]);
    }

    public function edit(Request $request, $id)
    {
        $trainingmedia = LTrainingMedia::select('*')
            ->where('training_media_id', '=', $id)
            ->first();

        return view('training.setup.trainingmedia.trainingmedia', [
            'trainingmedia' => $trainingmedia
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LTrainingMedia::all();
        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('training-media.training-media-edit', [$query->training_media_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {

        $response = $this->training_media_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-media.training-media-index');
    }

    public function update(Request $request, $id) {
        $response = $this->training_media_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-media.training-media-index');
    }

    private function training_media_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $training_media_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TRAINING_MEDIA_ID' => [
                    'value' => &$training_media_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_MEDIA_NAME_BN' => $postData['media_name_bn'],
                'P_MEDIA_NAME' => $postData['media_name'],
                'P_REMARKS' => $postData['remarks'],
                'p_active_yn' => ($postData['active_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_MEDIA_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function training_media_api_upd($request, $id)
    {
        $postData = $request->post();

        try {
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TRAINING_MEDIA_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INT
                ],
                'P_MEDIA_NAME_BN' => $postData['media_name_bn'],
                'P_MEDIA_NAME' => $postData['media_name'],
                'P_REMARKS' => $postData['remarks'],
                'p_active_yn' => ($postData['active_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_MEDIA_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
