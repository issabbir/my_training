<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Training\LExpertise;
use App\Entities\Training\TrainingType;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpertiseEntryController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.expertiseentry.expertise', [
            'expertiseEntry' => null,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $expertiseData = LExpertise::find($id);

        return view('training.setup.expertiseentry.expertise', [
            'expertiseEntry' => $expertiseData,
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LExpertise::orderBy('expertise_name', 'ASC')->get();
        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('expertise-entry.expertise-entry-edit', [$query->expertise_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {
        $response = $this->expertise_entry_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('expertise-entry.expertise-entry-index');
    }

    public function update(Request $request, $id) {
        $response = $this->expertise_entry_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('expertise-entry.expertise-entry-index');
    }

    private function expertise_entry_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $expertise_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_EXPERTISE_ID' => [
                    'value' => &$expertise_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_EXPERTISE_NAME' => $postData['expertise_name'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            //dd($params);
            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.EXPERTISE_LOOKUPS_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function expertise_entry_api_upd($request, $id)
    {

        $postData = $request->post();

        try {

            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_EXPERTISE_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_EXPERTISE_NAME' => $postData['expertise_name'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.EXPERTISE_LOOKUPS_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

}
