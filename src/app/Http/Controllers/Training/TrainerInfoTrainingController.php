<?php

namespace App\Http\Controllers\Training;

use App\Entities\Pmis\Employee\EmpTraining;
use App\Entities\Training\LTrainer;
use App\Entities\Training\LTrainerTrainingInfo;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerInfoTrainingController extends Controller
{
    use HasPermission;

    public function post($trainerId, Request $request)
    {
        $response = $this->trainer_training_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainer-information.trainer-training-get', $trainerId);
    }

    public function form($id, Request $request)
    {
        $trainerId = $id;
        $trainingList = LTrainerTrainingInfo::where('trainer_id', $trainerId)->get();

        $trainingId = $request->get('trainer_training_id');
        $trainingData = '';
        if ($trainingId) {
            $trainingData = LTrainerTrainingInfo::where('trainer_training_id', $trainingId)->where('trainer_id', $trainerId)->first();
        }

        $trainerDetails = LTrainer::find($trainerId);
        $empTrainingList = [];
        if ($trainerDetails) {
            $emp_id = $trainerDetails->emp_id;
            $empTrainingList = EmpTraining::select('*')
                ->leftJoin('pmis.l_training_type', 'pmis.l_training_type.training_type_id', '=', 'pmis.emp_training.training_type_id')
                ->where('pmis.emp_training.emp_id', $emp_id)
                ->get();
        }

        return view('training.trainerinfo.trainer_training',
            [
                'trainer_id' => $id,
                'trainingList' => $trainingList,
                'trainingView' => $trainingData,
                'empTrainingList' => $empTrainingList,
                'trainerDetails' => $trainerDetails
            ]);
    }

    public function update($trainerId, $ttid, Request $request)
    {
        $response = $this->trainer_training_api_upd($request, $ttid);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('trainer-information.trainer-training-get', [$trainerId, 'trainer_training_id' => $ttid]);
    }

    private function trainer_training_api_ins(Request $request)
    {
        $postData = $request->post();

        $trainerTrainingFileName = '';
        $trainerTrainingFileType = '';
        $trainerTrainingFileContent = '';
        $trainerTrainingFile = $request->file('training_attachment');

        if ($trainerTrainingFile) {
            $trainerTrainingFileName = $trainerTrainingFile->getClientOriginalName();
            $trainerTrainingFileType = $trainerTrainingFile->getMimeType();
            $trainerTrainingFileContent = base64_encode(file_get_contents($trainerTrainingFile->getRealPath()));
        }

        try {

            $trainer_training_id = null;
            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_TRAINER_TRAINING_ID' => $trainer_training_id,
                'P_TRAINING_NAME' => $postData['course_name'],
                'P_TRAINING_NAME_BN' => $postData['course_name_bn'],
                'P_INSTITUTE_NAME' => $postData['institute'],
                'P_INSTITUTE_BN' => $postData['institute_bn'],
                'P_INSTITUTE_ADDRESS' => $postData['institute_address'],
                'P_INSTITUTE_ADDRESS_BN' => $postData['Institute_address_bn'],
                'P_TRAINING_DURATION' => $postData['training_duration'],
                'P_TRAINING_CONTENT' => '',
                'P_TRAINING_ACHEVMENT' => '',
                'P_TRAINING_ATTACHMENT' => [
                    'value' => $trainerTrainingFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_TRAINING_ATTACHMENT_TYPE' => $trainerTrainingFileType,
                'P_TRAINING_ATTACHMENT_NAME' => $trainerTrainingFileName,
                'P_SPONSOR' => $postData['sponsor'],
                'P_COVERAGE' => $postData['coverage'],
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TMIS_TRAINER_TRAINING_ENTRY', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function trainer_training_api_upd(Request $request, $trainingId)
    {
        $postData = $request->post();
        $trainerTrainingFile = $request->file('training_attachment');

        if ($trainerTrainingFile) {
            $trainerTrainingFileName = $trainerTrainingFile->getClientOriginalName();
            $trainerTrainingFileType = $trainerTrainingFile->getMimeType();
            $trainerTrainingFileContent = base64_encode(file_get_contents($trainerTrainingFile->getRealPath()));
        } else {
            $trainerTrainingInfo = LTrainerTrainingInfo::find($trainingId);
            $trainerTrainingFileName = $trainerTrainingInfo->training_attachment_name;
            $trainerTrainingFileType = $trainerTrainingInfo->training_attachment_type;
            $trainerTrainingFileContent = $trainerTrainingInfo->training_attachment;
        }

        try {
            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_TRAINER_TRAINING_ID' => $trainingId,
                'P_TRAINING_NAME' => $postData['course_name'],
                'P_TRAINING_NAME_BN' => $postData['course_name_bn'],
                'P_INSTITUTE_NAME' => $postData['institute'],
                'P_INSTITUTE_BN' => $postData['institute_bn'],
                'P_INSTITUTE_ADDRESS' => $postData['institute_address'],
                'P_INSTITUTE_ADDRESS_BN' => $postData['Institute_address_bn'],
                'P_TRAINING_DURATION' => $postData['training_duration'],
                'P_TRAINING_CONTENT' => '',
                'P_TRAINING_ACHEVMENT' => '',
                'P_TRAINING_ATTACHMENT' => [
                    'value' => $trainerTrainingFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_TRAINING_ATTACHMENT_TYPE' => $trainerTrainingFileType,
                'P_TRAINING_ATTACHMENT_NAME' => $trainerTrainingFileName,
                'P_SPONSOR' => $postData['sponsor'],
                'P_COVERAGE' => $postData['coverage'],
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TMIS_TRAINER_TRAINING_UPD', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
