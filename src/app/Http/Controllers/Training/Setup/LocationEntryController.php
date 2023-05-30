<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Admin\LGeoCountry;
use App\Entities\Training\LTrainingLocation;
use App\Entities\Training\TrainingType;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationEntryController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.locationentry.locationentry', [
            'locationentry' => null,
            'countrylist' => LGeoCountry::all(),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $locationentry = LTrainingLocation::select('*')
            ->where('location_id', '=', $id)
            ->first();

        return view('training.setup.locationentry.locationentry', [
            'locationentry' => $locationentry,
            'countrylist' => LGeoCountry::all(),
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LTrainingLocation::orderBy('location_name', 'ASC')->get();
        return datatables()->of($queryResult)
            ->addColumn('action', function($query) {
                return '<a href="'. route('location-entry.location-entry-edit', [$query->location_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {
        $response = $this->location_entry_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('location-entry.location-entry-index');
    }

    public function update(Request $request, $id) {
        $response = $this->location_entry_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('location-entry.location-entry-index');
    }

    private function location_entry_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $location_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_LOCATION_ID' => [
                    'value' => &$location_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_LOCATION_NAME' => $postData['location_name'],
                'P_LOCATION_BNG' => $postData['location_name_bng'],
                'P_BUILDING_NAME' => $postData['building_name'],
                'P_BUILDING_NAME_BNG' => $postData['building_name_bng'],
                'P_FLOOR_NAME' => $postData['floor_name'],
                'P_FLOOR_NAME_BNG' => $postData['floor_name_bng'],
                'P_LOCATION_ADDRESS' => $postData['location_address'],
                'P_COUNTRY_ID' => $postData['country_name'],
                'P_REMARKS' => $postData['remarks'],
                'p_insert_by' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_LOCATION_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function location_entry_api_upd($request, $id)
    {
        $postData = $request->post();

        try {
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_LOCATION_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_LOCATION_NAME' => $postData['location_name'],
                'P_LOCATION_BNG' => $postData['location_name_bng'],
                'P_BUILDING_NAME' => $postData['building_name'],
                'P_BUILDING_NAME_BNG' => $postData['building_name_bng'],
                'P_FLOOR_NAME' => $postData['floor_name'],
                'P_FLOOR_NAME_BNG' => $postData['floor_name_bng'],
                'P_LOCATION_ADDRESS' => $postData['location_address'],
                'P_COUNTRY_ID' => $postData['country_name'],
                'P_REMARKS' => $postData['remarks'],
                'p_insert_by' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_LOCATION_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

}
