<?php

namespace App\Http\Controllers\Training;

use App\Entities\Pmis\Employee\EmpExperience;
use App\Entities\Training\LTrainer;
use App\Entities\Training\LTrainerExp;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerInfoExperienceController extends Controller
{
    use HasPermission;

    public function post($trainerId, Request $request)
    {
        $response = $this->trainer_experience_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainer-information.trainer-experience-get', $trainerId);
    }

    public function form($id, Request $request)
    {
        $trainerId = $id;
        $experienceList = LTrainerExp::where('trainer_id', $trainerId)->get();

        $experienceId = $request->get('exp_id');
        $experienceData = '';
        if ($experienceId) {
            $experienceData = LTrainerExp::where('exp_id', $experienceId)->where('trainer_id', $trainerId)->first();
        }

        $trainerDetails = LTrainer::find($trainerId);
        $empExperienceList = [];
        if ($trainerDetails) {
            $emp_id = $trainerDetails->emp_id;
            $empExperienceList = EmpExperience::select('*')
                ->where('pmis.emp_experience.emp_id', $emp_id)
                ->get();
        }

        return view('training.trainerinfo.trainer_experience',
            [
                'trainer_id' => $id,
                'experienceList' => $experienceList,
                'experienceView' => $experienceData,
                'empExperienceList' => $empExperienceList,
                'trainerDetails' => $trainerDetails
            ]);
    }

    public function update($trainerId, $eid, Request $request)
    {
        $response = $this->trainer_experience_api_upd($request, $eid);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('trainer-information.trainer-experience-get', [$trainerId, 'exp_id' => $eid]);
    }

    private function trainer_experience_api_ins(Request $request)
    {
        $postData = $request->post();

        $startDate = isset($postData['start_date']) ? date('Y-m-d', strtotime($postData['start_date'])) : '';
        $endDate = isset($postData['end_date']) ? date('Y-m-d', strtotime($postData['end_date'])) : '';

        $trainerExperienceFileName = '';
        $trainerExperienceFileType = '';
        $trainerExperienceFileContent = '';
        $trainerExperienceFile = $request->file('experience_letter');

        if ($trainerExperienceFile) {
            $trainerExperienceFileName = $trainerExperienceFile->getClientOriginalName();
            $trainerExperienceFileType = $trainerExperienceFile->getMimeType();
            $trainerExperienceFileContent = base64_encode(file_get_contents($trainerExperienceFile->getRealPath()));
        }

        $trainerReleaseFileName = '';
        $trainerReleaseFileType = '';
        $trainerReleaseFileContent = '';
        $trainerReleaseFile = $request->file('release_letter');

        if ($trainerReleaseFile) {
            $trainerReleaseFileName = $trainerReleaseFile->getClientOriginalName();
            $trainerReleaseFileType = $trainerReleaseFile->getMimeType();
            $trainerReleaseFileContent = base64_encode(file_get_contents($trainerReleaseFile->getRealPath()));
        }

        try {

            $exp_id = null;
            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_EXP_ID' => $exp_id,
                'P_ORGANIZATION_NAME' => $postData['organization_name'],
                'P_ORGANIZATION_BN' => $postData['organization_name_bn'],
                'P_DESIGNATION' => $postData['designation'],
                'P_DESIGNATION_BN' => $postData['designation_bn'],
                'P_START_DATE' => $startDate,
                'P_END_DATE' => $endDate,
                'P_ORGANIZATION_ADDRESS' => $postData['organization_address'],
                'P_ORGANIZATION_ADDRESS_BN' => $postData['organization_address_bn'],
                'P_EXP_LETTER_PHOTO' => [
                    'value' => $trainerExperienceFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_RELEASE_LETTER_PHOTO' => [
                    'value' => $trainerReleaseFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_EXP_LETTER_PHOTO_TYPE' => $trainerExperienceFileType,
                'P_EXP_LETTER_PHOTO_NAME' => $trainerExperienceFileName,
                'P_RELEASE_LETTER_P_TYPE' => $trainerReleaseFileType,
                'P_RELEASE_LETTER_P_NAME' => $trainerReleaseFileName,
                'P_CURRENT_JOB_YN' => ($postData['current_job_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TMIS_TRAINER_EXP_PR', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function trainer_experience_api_upd(Request $request, $experienceId)
    {
        $postData = $request->post();

        $startDate = isset($postData['start_date']) ? date('Y-m-d', strtotime($postData['start_date'])) : '';
        $endDate = isset($postData['end_date']) ? date('Y-m-d', strtotime($postData['end_date'])) : '';

        $trainerExperienceFile = $request->file('experience_letter');

        if ($trainerExperienceFile) {
            $trainerExperienceFileName = $trainerExperienceFile->getClientOriginalName();
            $trainerExperienceFileType = $trainerExperienceFile->getMimeType();
            $trainerExperienceFileContent = base64_encode(file_get_contents($trainerExperienceFile->getRealPath()));
        } else {
            $experienceInfo = LTrainerExp::find($experienceId);
            $trainerExperienceFileName = $experienceInfo->exp_letter_photo_name;
            $trainerExperienceFileType = $experienceInfo->exp_letter_photo_type;
            $trainerExperienceFileContent = $experienceInfo->exp_letter_photo;
        }
        $trainerReleaseFile = $request->file('release_letter');
        if ($trainerReleaseFile) {
            $trainerReleaseFileName = $trainerReleaseFile->getClientOriginalName();
            $trainerReleaseFileType = $trainerReleaseFile->getMimeType();
            $trainerReleaseFileContent = base64_encode(file_get_contents($trainerReleaseFile->getRealPath()));
        } else {
            $experienceInfo = LTrainerExp::find($experienceId);
            $trainerReleaseFileName = $experienceInfo->release_letter_p_name;
            $trainerReleaseFileType = $experienceInfo->release_letter_p_type;
            $trainerReleaseFileContent = $experienceInfo->release_letter_photo;
        }

        try {
            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_EXP_ID' => $experienceId,
                'P_ORGANIZATION_NAME' => $postData['organization_name'],
                'P_ORGANIZATION_BN' => $postData['organization_name_bn'],
                'P_DESIGNATION' => $postData['designation'],
                'P_DESIGNATION_BN' => $postData['designation_bn'],
                'P_START_DATE' => $startDate,
                'P_END_DATE' => $endDate,
                'P_ORGANIZATION_ADDRESS' => $postData['organization_address'],
                'P_ORGANIZATION_ADDRESS_BN' => $postData['organization_address_bn'],
                'P_EXP_LETTER_PHOTO' => [
                    'value' => $trainerExperienceFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_RELEASE_LETTER_PHOTO' => [
                    'value' => $trainerReleaseFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_EXP_LETTER_PHOTO_TYPE' => $trainerExperienceFileType,
                'P_EXP_LETTER_PHOTO_NAME' => $trainerExperienceFileName,
                'P_RELEASE_LETTER_P_TYPE' => $trainerReleaseFileType,
                'P_RELEASE_LETTER_P_NAME' => $trainerReleaseFileName,
                'P_CURRENT_JOB_YN' => ($postData['current_job_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TMIS_TRAINER_EXP_UPD', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

}
