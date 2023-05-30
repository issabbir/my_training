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

class TrainerBillPreparationController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
        return view('training.trainerbillprep.entry.bill_preparation', [
            'trainingInfo' => TrainingInfo::all()
        ]);
    }

    public function datatable(Request $request)
    {
        $training_no = $request->get("training_no");
        $schedule_id = $request->get("schedule_id");
        $bill_date = $request->get("bill_date");

        $query = <<<QUERY
 SELECT TRAINER_ID,
       --TCD.SCHEDULE_DTL_ID,
       SCHEDULE_ID,
       TRAINER_NAME,
       TRAIN_INFO,
       INTERNAL_YN,
       TRAINER_EMP_ID,
       EMP,
       TIN,BANK_ACC,
       COUNT (*)TOTAL_DAYS,
       (:p_training_id )  training_id,
       (:p_bill_date )  bill_date
  FROM (SELECT TCD.TRAINER_ID,
               --TCD.SCHEDULE_DTL_ID,
               TCD.SCHEDULE_ID,
               LT.TRAINER_NAME,
               LT.INTERNAL_YN,
               ('(' || ttif.training_number || ') '|| ttif.training_title) TRAIN_INFO,
               (CASE
                    WHEN LT.INTERNAL_YN = 'Y'
                    THEN (SELECT EMP_ID FROM PMIS.EMPLOYEE PE WHERE EMP_ID = LT.EMP_ID)
                    ELSE LT.TRAINER_ID
                END) TRAINER_EMP_ID,
               (CASE
                    WHEN LT.INTERNAL_YN = 'Y'
                    THEN (SELECT (PE.EMP_NAME || ', ' || PLDE.DESIGNATION) EMP_NAME FROM PMIS.EMPLOYEE PE, PMIS.L_DESIGNATION PLDE WHERE PE.EMP_ID = LT.EMP_ID AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID)
                    ELSE LT.TRAINER_NAME || ', ' || LT.TRAINER_DESIGNATION
                END) EMP,
               (CASE
                    WHEN LT.INTERNAL_YN = 'Y'
                    THEN (SELECT EMP_TIN_NO  FROM PMIS.EMPLOYEE PE WHERE EMP_ID = LT.EMP_ID)
                    ELSE ''
                END)
                   TIN,
               (CASE
                    WHEN LT.INTERNAL_YN = 'Y'
                    THEN (SELECT EMP_BANK_ACC_NO FROM PMIS.EMPLOYEE PE WHERE EMP_ID = LT.EMP_ID)
                    ELSE 'NO'
                END)
                   BANK_ACC
          FROM TIMS.TRAINING_SCHEDULE_DTL TCD, TIMS.L_TRAINER LT, TIMS.TRAINING_SCHEDULE_MST TSM, TIMS.TRAINING_INFO TTIF
         WHERE     LT.TRAINER_ID = TCD.TRAINER_ID
               AND TSM.SCHEDULE_ID = TCD.SCHEDULE_ID
               AND ttif.training_id = tsm.training_id
               AND TTIF.TRAINING_ID = :p_training_id
               AND TCD.SCHEDULE_ID = :p_schedule_id)
GROUP BY TRAINER_ID,--TCD.SCHEDULE_DTL_ID,
SCHEDULE_ID, TRAINER_NAME, TRAIN_INFO, INTERNAL_YN,TRAINER_EMP_ID,EMP,TIN,BANK_ACC
QUERY;

        $billinfo = DB::select($query, ['p_training_id' => $training_no, 'p_schedule_id' => $schedule_id/*, 'p_duration' => $training_duration*/, 'p_bill_date' => $bill_date]);

        return datatables()->of($billinfo)
            ->addColumn('bill_rate', function ($data) {
                $html = <<<HTML
<select name="bill_rate[]"  class="form-control bill-rate" autocomplete="off"><option value="2000">2000</option><option value="2500">2500</option></select>
HTML;
                return $html;
            })
            ->addColumn('income_tax', function ($data) {
                $status = ($data->tin == null) ? 'selected' : '';
                //<select name="income_tax[]"  class="form-control" autocomplete="off"><option value="10">10%</option><option value="15" $status >15%</option></select>
                $html = <<<HTML
<select name="income_tax[]"  class="form-control" autocomplete="off"><option value="10">10%</option></select>
HTML;

                return $html;
            })
            ->addColumn('vat', function ($data) {
                $five = 5;
                $zero = 0;
                $status = ($data->tin != null) ? $zero : $five;
                $html = <<<HTML
                <div class="input-group">
                    <input type="text" name="vat[]"  id="vat" value="{$status}"  class="form-control vat-change" />
                    <div class="input-group-append">
                        <label class="input-group-text" id="vat">%</label>
                    </div>
                </div>
HTML;
                return $html;

            })
            ->addColumn('tin', function ($data) {
                if (!empty($data->tin)) {
                    return $data->tin;
                } else {
                    return 'NO';
                }
            })
            ->addColumn('bill_amount', function ($data) {
                $amount = $data->total_days * 2000;
                $html = <<<HTML
<input type="hidden" name="training_id[]" value="{$data->training_id}" />
<input type="hidden" name="trainer_id[]" value="{$data->trainer_emp_id}" />
<input type="hidden" name="schedule_id[]" value="{$data->schedule_id}" />
<input type="hidden" name="bill_date[]" value="{$data->bill_date}" />
<input type="hidden" name="duration[]" value="{$data->total_days}" />
<input type="hidden" name="tin[]" value="{$data->tin}" />
<input type="number" name="bill_amount[]" value="{$amount}" readonly class="form-control" autocomplete="off" />
HTML;
                return $html;
            })
            ->rawColumns(['bill_rate', 'bill_amount'])
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function post(Request $request)
    {
//        dd($request->all());
        $lastParams = [];
        DB::beginTransaction();

        try {
            foreach ($request->get('trainer_id') as  $indx => $value) {
//dd($indx);
                $trainer_bill_id = null;
                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");
                $bill_amount = $request->get('bill_amount')[$indx];

                $vat = $request->get('vat')[$indx];


//                $vat_amount = $bill_amount * 15/ 100;
                $vat_amount = $bill_amount * $vat / 100;
                /*if(strtoupper($request->get('tin')[$indx])=='NO'){
                    $income_tax = 0;
                }else{*/
                $income_tax = $bill_amount * $request->get('income_tax')[$indx] / 100;
                //}
                $total_dedaction = $vat_amount + $income_tax;
                $net_amount = $bill_amount - $total_dedaction;

                $params = [
                    "P_PAYEE_BILL_ID" => [
                        'value' => &$trainer_bill_id,
                        'type' => \PDO::PARAM_INPUT_OUTPUT,
                        'length' => 255
                    ],
                    "P_TRAINING_ID" => $request->get('training_id')[$indx],
                    "P_SCHEDULE_ID" => $request->get('schedule_id')[$indx],
                    "P_PAYEE_ID" => $request->get('trainer_id')[$indx],
                    "P_PAYMENT_DATE" => $request->get('bill_date')[$indx],
                    "P_VAT_AMOUNT" => $vat_amount,
                    "P_ITD_AMOUNT" => $income_tax,
                    "P_TOTAL_DEDUCATION" => $total_dedaction,
                    "P_PAYEE_TYPE_ID" => '2',
                    "P_DURATION" => $request->get('duration')[$indx],
                    "P_RATE" => $request->get('bill_rate')[$indx],
                    "P_TOTAL_AMOUNT" => $bill_amount,
                    "P_NET_AMOUNT" => $net_amount,
                    "P_REMARKS" => '',
                    "P_APPROVED_YN" => '',
                    "P_INSERT_BY" => auth()->id(),
                    "o_status_code" => &$status_code,
                    "o_status_message" => &$status_message
                ];

                DB::executeProcedure("TIMS.TRAINING_APPROVAL_PKG.STAFF_BILL_INFO_PR", $params);

                $lastParams = $params;

                if ($params['o_status_code'] != 1) {
                    DB::rollBack();
                    $params['html'] = view('training.trainerbillprep.entry.message')->with('params', $params)->render();
                }

                DB::commit();
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $exception->getMessage()];
        }

        $lastParams['html'] = view('training.trainerbillprep.entry.message')->with('params', $lastParams)->render();
        return $lastParams;
    }
}
