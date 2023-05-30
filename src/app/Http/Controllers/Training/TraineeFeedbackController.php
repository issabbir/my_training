<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\LFeedbackType;
use App\Entities\Training\Trainee;
use App\Entities\Training\TrainingScheduleMaster;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraineeFeedbackController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        $feed_back_count = count(LFeedbackType::all());
        return view('training.traineefeedback.traineefeedback', [
            'traineefeedback' => null,
            'traineeinfo' => Trainee::all(),
            'schedule' => TrainingScheduleMaster::with('training_info')->where('schedule_status_id', '!=' , '4')->get(),
            'feedback' => LFeedbackType::all(),
            'feed_back_count' => $feed_back_count,
        ]);
    }

    public function post(Request $request)
    {
        $response = $this->trainee_feedback_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }
        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainee-feedback.trainee-feedback-index');
    }

    private function trainee_feedback_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $feedback_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_FEEDBACK_ID' => [
                    'value' => &$feedback_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_FEEDBACK_DATE' => null,
                'P_SCHEDULE_ID' => $postData['schedule_id'],
                'P_TRAINEE_ID' => $postData['trainee_id'],
                'P_TOTAL_SCORE' => '',
                'P_TRAINING_SUGGESTION' => $postData['training_sugg'],
                'P_OVERALL_SATISFY_YN' => $postData['overall_sat_yn'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_FEEDBACK_MST_PR', $params);
            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }else{
                if ($request->get('rating'))
                {
                    $count = 0;
                    $feedback_id =$params['P_FEEDBACK_ID']['value'];
                    foreach ($request->get('rating') as $indx => $value)
                    {
                        $feedback_dtl_id = null;

                        $fulldata = $request->get('rating')[$indx];
                        $stringParts = explode("-", $fulldata);
                        $feedback_type_id  = $stringParts[0];
                        $rating = $stringParts[1];
                        $status_code = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params_dtl = [
                            "P_FEEDBACK_DTL_ID" => [
                                'value' => &$feedback_dtl_id,
                                'type' => \PDO::PARAM_INPUT_OUTPUT,
                                'length' => 255
                            ],
                            "P_FEEDBACK_ID" => $feedback_id,
                            "P_FEEDBACK_ITEM_ID" => $feedback_type_id,
                            "P_SCORE" => $rating,
                            "P_REMARKS" => '',
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "o_status_message" => &$status_message
                        ];

                        DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_FEEDBACK_DTL_PR", $params_dtl);$count++;

                    }
                }
            }
            return $params;
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }
}
