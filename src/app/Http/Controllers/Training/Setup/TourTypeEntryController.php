<?php

namespace App\Http\Controllers\Training\Setup;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\YesNoFlag;
use App\Traits\Security\HasPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Entities\Training\LTourTypes;

class TourTypeEntryController extends Controller
{
    use HasPermission;

    public function index()
    {
        return view('training.setup.tourtype.tour_type_entry', [
            'tourType' => null,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $tourTypeData = LTourTypes::find($id);

        return view('training.setup.tourtype.tour_type_entry', [
            'tourType' => $tourTypeData,
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LTourTypes::all();

        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->status_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('tour-type-entry.tour-type-entry-edit', [$query->tour_type_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a> <a href="'. route('tour-type-entry.tour-type-entry-delete', [$query->tour_type_id]) .'" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="bx bx-trash cursor-pointer text-danger"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {
        $response = $this->tour_type_entry_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('tour-type-entry.tour-type-entry-index');
    }

    public function update(Request $request, $id) {
        $response = $this->tour_type_entry_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('tour-type-entry.tour-type-entry-index');
    }

    public function delete(Request $request, $id) {
        $response = $this->tour_type_entry_api_delete($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('tour-type-entry.tour-type-entry-index');
    }

    public function tour_type_entry_api_delete($request, $id)
    {
        $deleteTourType = LTourTypes::find($id);
        $deleteTourType->delete();

        if($deleteTourType)
        {
            $params = [
                'o_status_code' => 1,
                'o_status_message' => 'SUCCESSFULLY DELETED RECORD',
            ];
        }
        else
        {
            $params = [
                'o_status_code' => 99,
                'o_status_message' => 'PROBLEM OCCURRED',
            ];
        }

        return $params;
    }

    private function tour_type_entry_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $id = LTourTypes::max('tour_type_id');

            $newTourType = new LTourTypes;
            $newTourType->tour_type_id = ++$id;
            $newTourType->tour_type_name = $request->tour_type_name;
            $newTourType->status_yn = $request->active_yn;
            $newTourType->insert_by = Auth::user()->emp_id;
            $newTourType->insert_date = Carbon::now();

            $newTourType->save();

            return $params = [
                'o_status_code' => 1,
                'o_status_message' => 'SUCCESSFULLY INSERTED RECORD',
            ];
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

    }

    private function tour_type_entry_api_upd($request, $id)
    {
        $postData = $request->post();

        try {
            $newTourType = LTourTypes::find($id);

            $newTourType->tour_type_name = $request->tour_type_name;
            $newTourType->status_yn = $request->active_yn;
            $newTourType->update_by = Auth::user()->emp_id;
            $newTourType->update_date = Carbon::now();

            $newTourType->save();

            return $params = [
                'o_status_code' => 1,
                'o_status_message' => 'SUCCESSFULLY UPDATED RECORD',
            ];
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
    }
}
