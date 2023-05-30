<?php

namespace App\Http\Controllers\Training\Setup;


use App\Entities\Training\LTourSponsor;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForeignTourSponsorController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.foreigntoursponsor.foreign_tour_sponsor', [
            'foreignTourSponsor' => null,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $foreignTourSponsor = LTourSponsor::find($id);

        return view('training.setup.foreigntoursponsor.foreign_tour_sponsor', [
            'foreignTourSponsor' => $foreignTourSponsor,
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LTourSponsor::all();
        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('foreign-tour-sponsor.foreign-tour-sponsor-edit', [$query->tour_sponser_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {
        $response = $this->foreign_tour_sponsor_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('foreign-tour-sponsor.foreign-tour-sponsor-index');
    }

    public function update(Request $request, $id) {
        $response = $this->foreign_tour_sponsor_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('foreign-tour-sponsor.foreign-tour-sponsor-index');
    }

    private function foreign_tour_sponsor_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $tour_sponsor_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TOUR_SPONSER_ID' => [
                    'value' => &$tour_sponsor_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_SPONSER_NAME' => $postData['sponsor_name'],
                'P_SPONSER_NAME_BN' => $postData['sponsor_name_bn'],
                'P_REMARKS' => $postData['remarks'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.FOREIGN_SPONSER_PR', $params);

        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function foreign_tour_sponsor_api_upd($request, $id)
    {
        $postData = $request->post();

        try {
            $tour_sponsor_id = $id;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_TOUR_SPONSER_ID' => [
                    'value' => &$tour_sponsor_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_SPONSER_NAME' => $postData['sponsor_name'],
                'P_SPONSER_NAME_BN' => $postData['sponsor_name_bn'],
                'P_REMARKS' => $postData['remarks'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.FOREIGN_SPONSER_PR', $params);

        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
