<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Training\LExamType;
use App\Entities\Training\TrainingType;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamTypeController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.examtype.examtype', [
            'examtype' => null
        ]);
    }

    public function edit(Request $request, $id)
    {
        $examtype = LExamType::select('*')
            ->where('exam_type_id', '=', $id)
            ->first();

        return view('training.setup.examtype.examtype', [
            'examtype' => $examtype
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LExamType::all();
        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('exam-type.exam-type-edit', [$query->exam_type_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {

        $response = $this->exam_type_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('exam-type.exam-type-index');
    }

    public function update(Request $request, $id) {
        $response = $this->exam_type_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('exam-type.exam-type-index');
    }

    private function exam_type_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $exam_type_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_EXAM_TYPE_ID' => [
                    'value' => &$exam_type_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_EXAM_TYPE_NAME' => $postData['exam_type_name'],
                'P_EXAM_TYPE_BN' => $postData['exam_type_name_bn'],
                'P_ACTIVE_YN' => ($postData['active_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_EVU_EXAM_TYPE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function exam_type_api_upd($request, $id)
    {
        $postData = $request->post();

        try {
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_EXAM_TYPE_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_EXAM_TYPE_NAME' => $postData['exam_type_name'],
                'P_EXAM_TYPE_BN' => $postData['exam_type_name_bn'],
                'P_ACTIVE_YN' => ($postData['active_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_EVU_EXAM_TYPE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
