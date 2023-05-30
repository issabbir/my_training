<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Training\LPreRequsit;
use App\Entities\Training\TrainingType;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LPreRequisiteEntryController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.pre-requisiteentry.pre_requisite', [
            'preRequisiteEntry' => null,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $preRequisiteData = LPreRequsit::find($id);

        return view('training.setup.pre-requisiteentry.pre_requisite', [
            'preRequisiteEntry' => $preRequisiteData,
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LPreRequsit::all();
        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('pre-requisite-entry.pre-requisite-entry-edit', [$query->requsit_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {
        $response = $this->pre_requisite_entry_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('pre-requisite-entry.pre-requisite-entry-index');
    }

    public function update(Request $request, $id) {
        $response = $this->pre_requisite_entry_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('pre-requisite-entry.pre-requisite-entry-index');
    }


    private function pre_requisite_entry_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $pre_requisite_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_REQUSIT_ID' => [
                    'value' => &$pre_requisite_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_PRE_REQUSIT_NAME' => $postData['pre_requisite_name'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.PRE_REQUSIT_LOOKUPS_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function pre_requisite_entry_api_upd($request, $id)
    {
        $postData = $request->post();

        try {

            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_REQUSIT_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_PRE_REQUSIT_NAME' => $postData['pre_requisite_name'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.PRE_REQUSIT_LOOKUPS_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
