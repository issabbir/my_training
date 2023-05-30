<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\LExpertise;
use App\Entities\Training\LTrainerExpertise;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerInfoExpertiseController extends Controller
{
    use HasPermission;

    public function post($trainerId, Request $request)
    {
        $response = $this->trainer_expertise_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainer-information.trainer-expertise-get', $trainerId);
    }

    public function form($id, Request $request)
    {
        $trainerId = $id;
        $expertiseList = LTrainerExpertise::with(['l_exper'])->where('trainer_id', $trainerId)->get();

        $expertiseId = $request->get('trainer_exp_id');
        $expertiseData = '';
        if ($expertiseId) {
            $expertiseData = LTrainerExpertise::where('trainer_exp_id', $expertiseId)->where('trainer_id', $trainerId)->first();
        }

        return view('training.trainerinfo.trainer_expertise',
            [
                'trainer_id' => $id,
                'expertiseList' => $expertiseList,
                'expertiseView' => $expertiseData,
                'lExpertise' => LExpertise::all()
            ]);
    }

    public function update($trainerId, $texid, Request $request)
    {
        $response = $this->trainer_expertise_api_upd($request, $texid);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('trainer-information.trainer-expertise-get', [$trainerId, 'trainer_exp_id' => $texid]);
    }

    private function trainer_expertise_api_ins(Request $request)
    {
        $postData = $request->post();

        try {

            $trainer_expertise_id = null;
            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_EXP_ID' => $trainer_expertise_id,
                'P_EXPERTISE_ID' => $postData['expertise_id'],
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_EXPERTISE_NAME' => '',
                'P_ACTIVE_YN' => ($postData['training_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TMIS_TRAINER_EXPERTISE_ENTRY', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function trainer_expertise_api_upd(Request $request, $expertiseId)
    {
        $postData = $request->post();

        try {

            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINER_EXP_ID' => $expertiseId,
                'P_EXPERTISE_ID' => $postData['expertise_id'],
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_EXPERTISE_NAME' => '',
                'P_ACTIVE_YN' => ($postData['training_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TMIS_TRAINER_EXPERTISE_UPD', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

}
