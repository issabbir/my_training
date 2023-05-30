<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\LTrainer;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerInfoController extends Controller
{
    use HasPermission;

    protected $trainerId;

    public function form(Request $request)
    {
        $trainerId = $request->get('trainer_id') ?: 0;
        $trainer = LTrainer::find($trainerId);
        return view('training.trainerinfo.trainer_info',
            [
                'trainerInfo' => $trainer,
                'trainer_id' => $trainerId
            ]);
    }

    public function post(Request $request)
    {
        $response = $this->trainer_info_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('trainer-information.trainer-info-get', ['trainer_id' => isset($this->trainerId) ? $this->trainerId : 0]);
    }

    public function dataTableIndex()
    {
        return view('training.trainerinfo.trainerinfo_list');
    }

    public function dataTableList()
    {
        $queryResult = LTrainer::all();

        return datatables()->of($queryResult)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('trainer-information.trainer-info-get', ['trainer_id' => $query->trainer_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }


    public function update($trainerId, Request $request)
    {
        $response = $this->trainer_info_api_upd($request, $trainerId);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('trainer-information.trainer-info-get', ['trainer_id' => $response['P_TRAINER_ID']]);
    }

    private function trainer_info_api_ins(Request $request)
    {
        $postData = $request->post();
        $emp_id = isset($postData['emp_id']) ? ($postData['emp_id']) : '';

        $trainerPhotoName = '';
        $trainerPhotoType = '';
        $trainerPhotoContent = '';
        $trainerPhotoFile = $request->file('trainer_photo');

        if ($trainerPhotoFile) {
            $trainerPhotoName = $trainerPhotoFile->getClientOriginalName();
            $trainerPhotoType = $trainerPhotoFile->getMimeType();
            $trainerPhotoContent = base64_encode(file_get_contents($trainerPhotoFile->getRealPath()));
        }

        try {
            $trainer_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_ID' => [
                    'value' => &$trainer_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_TRAINER_NO' => $postData['trainer_no'],
                'P_INTERNAL_YN' => ($postData['active_yn1'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_EMP_ID' => $emp_id,
                'P_ADDRESS_ID' => null,
                'P_TRAINER_NAME' => $postData['emp_name'],
                'P_TRAINER_NAME_BN' => $postData['emp_name_bng'],
                'P_TRAINER_DESIGNATION' => $postData['emp_designation'],
                'P_MOBILE_NO' => $postData['mobile_no'],
                'P_EMAIL_ADD' => $postData['email_address'],
                'P_WORKPLACE' => $postData['work_place'],
                'P_EXPERTISE' => $postData['expertise'],
                'P_DISTINCTION' => $postData['distinction'],
                'P_TRAINER_ACTIVE_YN' => ($postData['trainer_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_EMG_CELL_NUMBER' => $postData['emergency_cell_No'],
                'P_JOB_RESPONSIBILITY' => $postData['job_responsibility'],
                'P_NID' => $postData['nid'],
                'P_PERFORMANCE' => $postData['performance'],
                'P_REMUNERATION' => $postData['remuneration'],
                'P_TRAINER_PHOTO' => [
                    'value' => $trainerPhotoContent,
                    'type' => SQLT_CLOB,
                ],
                'P_TRAINER_PHOTO_NAME' => $trainerPhotoName,
                'P_TRAINER_PHOTO_TYPE' => $trainerPhotoType,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => [
                    'value' => &$this->trainerId,
                    'type' => \PDO::PARAM_INT
                ],
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TIMS_TRAINER_BASIC_ENTRY', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    public function trainer_info_api_upd(Request $request, $trainerId)
    {
        $postData = $request->post();

        $emp_id = isset($postData['emp_id']) ? ($postData['emp_id']) : '';

        $trainerPhotoFile = $request->file('trainer_photo');

        if ($trainerPhotoFile) {
            $trainerPhotoName = $trainerPhotoFile->getClientOriginalName();
            $trainerPhotoType = $trainerPhotoFile->getMimeType();
            $trainerPhotoContent = base64_encode(file_get_contents($trainerPhotoFile->getRealPath()));
        } else {
            $trainerInfo = LTrainer::find($trainerId);
            $trainerPhotoName = $trainerInfo->trainer_photo_name;
            $trainerPhotoType = $trainerInfo->trainer_photo_type;
            $trainerPhotoContent = $trainerInfo->trainer_photo;
        }

        try {

            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_ID' => $trainerId,
                'P_TRAINER_NO' => $postData['trainer_no'],
                'P_INTERNAL_YN' => ($postData['active_yn1'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_EMP_ID' => $emp_id,
                'P_ADDRESS_ID' => null,
                'P_TRAINER_NAME' => $postData['emp_name'],
                'P_TRAINER_NAME_BN' => $postData['emp_name_bng'],
                'P_TRAINER_DESIGNATION' => $postData['emp_designation'],
                'P_MOBILE_NO' => $postData['mobile_no'],
                'P_EMAIL_ADD' => $postData['email_address'],
                'P_WORKPLACE' => $postData['work_place'],
                'P_EXPERTISE' => $postData['expertise'],
                'P_DISTINCTION' => $postData['distinction'],
                'P_TRAINER_ACTIVE_YN' => ($postData['trainer_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,

                'P_EMG_CELL_NUMBER' => $postData['emergency_cell_No'],
                'P_JOB_RESPONSIBILITY' => $postData['job_responsibility'],
                'P_NID' => $postData['nid'],
                'P_PERFORMANCE' => $postData['performance'],
                'P_REMUNERATION' => $postData['remuneration'],
                'P_TRAINER_PHOTO' => [
                    'value' => $trainerPhotoContent,
                    'type' => SQLT_CLOB,
                ],
                'P_TRAINER_PHOTO_NAME' => $trainerPhotoName,
                'P_TRAINER_PHOTO_TYPE' => $trainerPhotoType,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TIMS_TRAINER_BASIC_UPD', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

}
