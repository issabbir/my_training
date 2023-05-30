<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Admin\LGeoThana;
use App\Entities\Pmis\Employee\EmpAddress;
use App\Entities\Training\LAddress;
use App\Entities\Training\LTrainer;
use App\Entities\Training\LTrainerAddress;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerInfoAddressController extends Controller
{
    use HasPermission;

    public function post($trainerId, Request $request)
    {
        $response = $this->trainer_address_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainer-information.trainer-address-get', $trainerId);
    }

    public function form($id, Request $request)
    {
        $trainerId = $id;
        $addressList = LTrainerAddress::with(['add_type', 'div_name', 'dis_name'])->where('trainer_id', $trainerId)->get();
        $addressId = $request->get('address_id');
        $addressData = '';
        if ($addressId) {
            $addressData = LTrainerAddress::where('address_id', $addressId)->where('trainer_id', $trainerId)->first();
        }

        $trainerDetails = LTrainer::find($trainerId);
        $empAddressList = [];
        if ($trainerDetails) {
            $emp_id = $trainerDetails->emp_id;
            $empAddressList = EmpAddress::select('*')
                ->leftJoin('pmis.l_geo_division', 'pmis.l_geo_division.geo_division_id', '=', 'pmis.emp_addresses.division_id')
                ->leftJoin('pmis.l_geo_district', 'pmis.l_geo_district.geo_district_id', '=', 'pmis.emp_addresses.district_id')
                ->leftJoin('pmis.l_address_type', 'pmis.l_address_type.address_type_id', '=', 'pmis.emp_addresses.address_type_id')
                ->where('pmis.emp_addresses.emp_id', $emp_id)
                ->get();
        }

        return view('training.trainerinfo.trainer_address',
            [
                'trainer_id' => $id,
                'address_type' => LAddress::all(),
                'addressList' => $addressList,
                'addressView' => $addressData,
                'divisions' => LGeoDivision::all(),
                'districts' => LGeoDistrict::all(),
                'thanas' => LGeoThana::all(),
                'empAddressList' => $empAddressList,
                'trainerDetails' => $trainerDetails
            ]);
    }

    public function update($trainerId, $aid, Request $request)
    {
        $response = $this->trainer_address_api_upd($request, $aid);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('trainer-information.trainer-address-get', [$trainerId, 'address_id' => $aid]);
    }

    private function trainer_address_api_ins(Request $request)
    {
        $postData = $request->post();
        try {

            $address_id = null;
            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_ADDRESS_ID' => $address_id,
                'P_ADDRESS_TYPE_ID' => $postData['address_type_id'],
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_ADDRESS_TYPE' => null,
                'P_ADDRESS_LINE' => $postData['address_line'],
                'P_GEO_DIVISION_ID' => $postData['geo_division_id'],
                'P_GEO_DIVISION_NAME' => null,
                'P_GEO_DISTRICT_ID' => $postData['geo_district_id'],
                'P_GEO_DISTRICT_NAME' => null,
                'P_GEO_THANA_ID' => $postData['geo_thana_id'],
                'P_GEO_THANA_NAME' => null,
                'P_POST_OFFICE' => $postData['post_office'],
                'P_POST_CODE' => $postData['post_code'],
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TIMS_TRAINER_ADDRESS_ENTRY', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function trainer_address_api_upd(Request $request, $addressId)
    {
        $postData = $request->post();
        try {

            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_ADDRESS_ID' => $addressId,
                'P_ADDRESS_TYPE_ID' => $postData['address_type_id'],
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_ADDRESS_TYPE' => null,
                'P_ADDRESS_LINE' => $postData['address_line'],
                'P_GEO_DIVISION_ID' => $postData['geo_division_id'],
                'P_GEO_DIVISION_NAME' => null,
                'P_GEO_DISTRICT_ID' => $postData['geo_district_id'],
                'P_GEO_DISTRICT_NAME' => null,
                'P_GEO_THANA_ID' => $postData['geo_thana_id'],
                'P_GEO_THANA_NAME' => null,
                'P_POST_OFFICE' => $postData['post_office'],
                'P_POST_CODE' => $postData['post_code'],
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TIMS_TRAINER_ADDRESS_UPD', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
