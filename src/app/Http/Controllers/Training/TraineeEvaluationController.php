<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\LTrainer;
use App\Entities\Training\Trainee;
use App\Entities\Training\TrainingInfo;
use App\Entities\Training\TrainingScheduleMaster;
use App\Http\Controllers\Controller;
use App\Managers\TrainingManager;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraineeEvaluationController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
        return view('training.traineeevaluation.entry.trainee_evaluation', [
            'traineeEvaluation' => null,
            'trainingInfo' => TrainingInfo::all(),
            'lTrainer' => LTrainer::all(),
            'trainee' => Trainee::all(),
            'trainingschedule' => TrainingScheduleMaster::with('training_info')->where('schedule_status_id', '!=', '4')->get()
        ]);
    }

    public function datatable(Request $request)
    {
        $traineeinfo = $this->trainingManager->evaluationEntrySearch($request->get("exam_type"), $request->get("batch_id"));

        return datatables()->of($traineeinfo)
            ->addColumn('exam_score', function ($data) {
                $html = <<<HTML
<input type="hidden" name="exam_type_id[]" value="{$data->exam_type_id}" />
<input type="hidden" name="evaluation_id[]" value="{$data->evaluation_id}" />
<input type="hidden" name="passing_score[]" class="pass-mark" value="{$data->pass_marks}" />
<input type="number" name="exam_score[]"  class="form-control exam-score" autocomplete="off" />
HTML;
                return $html;
            })
            ->addColumn('remark', function ($data) {
                $html = <<<HTML
<input type="text" name="remark[]"  value="" onkeyup="this.value = this.value.toUpperCase();" class="form-control result-mark" autocomplete="off" />
HTML;
                return $html;
            })
            ->rawColumns(['exam_score', 'remark'])
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
        $lastParams = [];
        DB::beginTransaction();

        try {
            foreach ($request->get('exam_type_id') as $indx => $value) {
                $trainee_evaluation_dtl_id = null;
                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");
                $params = [
                    "P_EVA_DTL_ID" => [
                        'value' => &$trainee_evaluation_dtl_id,
                        'type' => \PDO::PARAM_INPUT_OUTPUT,
                        'length' => 255
                    ],
                    "P_EVALUATION_ID" => $request->get('evaluation_id')[$indx],
                    "P_EXAM_TYPE_ID" => $request->get('exam_type_id')[$indx],
                    "P_EXAM_SCORE" => $request->get('exam_score')[$indx],
                    "P_IND_P_SCORE" => $request->get('passing_score')[$indx],
                    "P_REMARKS" => $request->get('remark')[$indx],
                    "P_INSERT_BY" => auth()->id(),
                    "o_status_code" => &$status_code,
                    "o_status_message" => &$status_message
                ];

                DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_EVALUTION_DTL_PR", $params);
                $lastParams = $params;

                if ($params['o_status_code'] != 1) {
                    DB::rollBack();
                    $params['html'] = view('training.traineeevaluation.entry.message')->with('params', $params)->render();
                }

                DB::commit();
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $exception->getMessage()];
        }

        $lastParams['html'] = view('training.traineeevaluation.entry.message')->with('params', $lastParams)->render();
        return $lastParams;
    }
}
