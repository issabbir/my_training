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

class TraineeBillPreparationUpdateController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
        return view('training.traineebillprep.update.trainee_bill_preparation_update', [
            'trainingInfo' => TrainingInfo::all()
        ]);
    }

    public function datatable(Request $request)
    {
        $training_no = $request->get("training_no");
        $schedule_id = $request->get("schedule_id");

        $query = <<<QUERY
 SELECT TTIF.TRAINING_ID AS training_id,
       TB.SCHEDULE_ID AS schedule_id,
       TB.PAYEE_BILL_ID as payee_bill_id,
       TT.TRAINEE_ID   AS trainee_id,
       ('(' || TTIF.TRAINING_NUMBER || ') '|| TTIF.TRAINING_TITLE) training_info,
       TT.TRAINEE_NAME          AS trainee_name,
       TT.ORGANIZATION_NAME     AS organization_name,
       TT.DESIGNATION_NAME AS desig_name,
       TT.DEPARTMENT_NAME AS dept_name,
       TO_CHAR( PAYMENT_DATE, 'YYYY-MM-DD' )  bill_date,
       TB.DURATION AS duration,
       TB.RATE  AS rate,
       TOTAL_AMOUNT AS total_amount
  FROM TIMS.TRAINEE                      TT,
       TIMS.TRAINING_INFO                TTIF,
       TIMS.BILL_INFO                 TB
 WHERE TTIF.TRAINING_ID = TB.TRAINING_ID
       AND TT.TRAINEE_ID = TB.PAYEE_ID
       AND TB.PAYEE_TYPE_ID = '1'
       AND TB.SCHEDULE_ID = :p_schedule_id
       AND TB.TRAINING_ID = :p_training_id
UNION ALL
SELECT TTIF.TRAINING_ID AS training_id,
       TB.SCHEDULE_ID AS schedule_id,
       TB.PAYEE_BILL_ID as payee_bill_id,
       PE.EMP_ID      AS trainee_id,
       ('(' || TTIF.TRAINING_NUMBER || ') '|| TTIF.TRAINING_TITLE) training_info,
       PE.EMP_NAME             AS trainee_name,
       'CPA'                   AS organization_name,
       PLDE.DESIGNATION AS desig_name,
       PLD.DEPARTMENT_NAME AS dept_name,
       TO_CHAR( PAYMENT_DATE, 'YYYY-MM-DD' )  bill_date,
       TB.DURATION AS duration,
       TB.RATE  AS rate,
       TOTAL_AMOUNT AS total_amount
  FROM PMIS.EMPLOYEE                     PE,
       TIMS.TRAINING_INFO                TTIF,
       TIMS.BILL_INFO                 TB,
       PMIS.L_DEPARTMENT PLD, PMIS.L_DESIGNATION PLDE
 WHERE PE.DPT_DEPARTMENT_ID = PLD.DEPARTMENT_ID
       AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
       AND TTIF.TRAINING_ID = TB.TRAINING_ID
       AND TB.PAYEE_TYPE_ID = '1'
       AND PE.EMP_ID  = TB.PAYEE_ID
       AND TB.SCHEDULE_ID = :p_schedule_id
       AND TB.TRAINING_ID = :p_training_id
QUERY;

        $billInfo = DB::select($query, ['p_training_id' => $training_no, 'p_schedule_id' => $schedule_id]);

        return datatables()->of($billInfo)
            ->addColumn('bill_rate', function ($data) {
                $html = <<<HTML
<!--<input type="number" name="bill_rate[]"  value="" onkeyup="this.value = this.value.toUpperCase();" class="form-control bill-rate" autocomplete="off" /> --->
<select name="bill_rate[]"  class="form-control bill-rate" autocomplete="off"><option value="400">400</option><option value="500">500</option></select>
HTML;
                return $html;
            })
            ->addColumn('bill_amount', function ($data) {
                $html = <<<HTML
<input type="hidden" name="payee_bill_id[]" value="{$data->payee_bill_id}" />
<input type="hidden" name="training_id[]" value="{$data->training_id}" />
<input type="hidden" name="schedule_id[]" value="{$data->schedule_id}" />
<input type="hidden" name="trainee_id[]" value="{$data->trainee_id}" />
<input type="hidden" name="bill_date[]" value="{$data->bill_date}" />
<input type="hidden" name="duration[]" value="{$data->duration}" />
<input type="number" name="bill_amount[]" readonly class="form-control" autocomplete="off" />
HTML;
                return $html;
            })
            ->rawColumns(['bill_rate', 'bill_amount'])
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
        $lastParams = [];
        DB::beginTransaction();

        try {
            foreach ($request->get('training_id') as $indx => $value) {
                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");
                //$att_id = sprintf("%4000s", "");
                $params = [
                    "P_TRAINEE_BILL_ID" => [
                        'value' => &$request->get('payee_bill_id')[$indx],
                        'type' => \PDO::PARAM_INPUT_OUTPUT,
                        'length' => 255
                    ],
                    "P_TRAINING_ID" => $request->get('training_id')[$indx],
                    "P_SCHEDULE_ID" => $request->get('schedule_id')[$indx],
                    "P_TRAINEE_ID" => $request->get('trainee_id')[$indx],
                    "P_PAYMENT_DATE" => $request->get('bill_date')[$indx],
                    "P_DURATION" => $request->get('duration')[$indx],
                    "P_RATE" => $request->get('bill_rate')[$indx],
                    "P_TOTAL_AMOUNT" => $request->get('bill_amount')[$indx],
                    "P_REMARKS" => '',
                    "P_APPROVED_YN" => '',
                    "P_INSERT_BY" => auth()->id(),
                    //"O_ATT_ID" => &$att_id,
                    "o_status_code" => &$status_code,
                    "o_status_message" => &$status_message
                ];

                DB::executeProcedure("TIMS.TRAINING_APPROVAL_PKG.TRAINEE_BILL_PR_UPD", $params);
                $lastParams = $params;

                if ($params['o_status_code'] != 1) {
                    DB::rollBack();
                    $params['html'] = view('training.traineebillprep.update.message')->with('params', $params)->render();
                }

                DB::commit();
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $exception->getMessage()];
        }

        $lastParams['html'] = view('training.traineebillprep.update.message')->with('params', $lastParams)->render();
        return $lastParams;
    }
}
