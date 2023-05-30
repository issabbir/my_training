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

class SstuffBillPreparationController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
        return view('training.sstuffbillprep.entry.bill_preparation', [
            'trainingInfo' => TrainingInfo::all()
        ]);
    }

    public function datatable(Request $request)
    {
        $training_no = $request->get("training_no");
        $schedule_id = $request->get("schedule_id");
        $bill_date = $request->get("bill_date");
        $training_duration = $request->get("training_duration");

        $query = <<<QUERY
SELECT to_number('0') SUPPORT_MEMBER_ID,
       TSM.SCHEDULE_ID schedule_id,
       TSM.COURSE_DIRECTOR_ID supp_stuff_id,
       (PE.EMP_NAME || ' (Course Director)') as supp_stuff_name,
       PLDE.DESIGNATION,
       PE.EMP_TIN_NO tin,
       PE.EMP_BANK_ACC_NO bank_acc,
       ('1500') as bill_rate,
       (:p_bill_date )  bill_date,
       (:p_duration) as duration,
       (:p_training_id) as training_id
  FROM TIMS.TRAINING_SCHEDULE_MST  TSM,
       PMIS.EMPLOYEE               PE,
       PMIS.L_DESIGNATION          PLDE
 WHERE     PE.EMP_ID = TSM.COURSE_DIRECTOR_ID
       AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
       AND TSM.SCHEDULE_ID = :p_schedule_mst_id
UNION ALL
 SELECT to_number('0') SUPPORT_MEMBER_ID,
       TSM.SCHEDULE_ID schedule_id,
       TTI.COORDINATION_EMP_ID supp_stuff_id,
       (PE.EMP_NAME || ' (Course Cordinator)') as supp_stuff_name,
       PLDE.DESIGNATION,
       PE.EMP_TIN_NO tin,
       PE.EMP_BANK_ACC_NO bank_acc,
       ('1200') as bill_rate,
       (:p_bill_date )  bill_date,
       (:p_duration) as duration,
       (:p_training_id) as training_id
  FROM TIMS.TRAINING_SCHEDULE_MST  TSM, TIMS.TRAINING_INFO TTI,
       PMIS.EMPLOYEE               PE,
       PMIS.L_DESIGNATION          PLDE
 WHERE     PE.EMP_ID = TTI.COORDINATION_EMP_ID
       AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
       AND TTI.TRAINING_ID = TSM.TRAINING_ID
       AND TSM.SCHEDULE_ID = :p_schedule_mst_id
UNION ALL
SELECT SSI.SUPPORT_MEMBER_ID,
       SSI.SCHEDULE_MST_ID schedule_id,
       SSI.EMP_ID supp_stuff_id,
       (PE.EMP_NAME || ' (Support Staff)') as supp_stuff_name,
       PLDE.DESIGNATION,
       PE.EMP_TIN_NO tin,
       PE.EMP_BANK_ACC_NO bank_acc,
       ('500') as bill_rate,
       (:p_bill_date )  bill_date,
       (:p_duration) as duration,
       (:p_training_id) as training_id
  FROM TIMS.SCHEDULE_SUPPORT_INFO  SSI,
       PMIS.EMPLOYEE               PE,
       PMIS.L_DESIGNATION          PLDE
 WHERE PE.EMP_ID = SSI.EMP_ID
 AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
 AND SSI.SCHEDULE_MST_ID = :p_schedule_mst_id
QUERY;

        $billinfo = DB::select($query, ['p_training_id' => $training_no, 'p_schedule_mst_id' => $schedule_id, 'p_duration' => $training_duration, 'p_bill_date' => $bill_date]);
//dd($billinfo);
        return datatables()->of($billinfo)
            ->addColumn('bill_rate', function ($data) {
                $html = <<<HTML
    <!--<select name="bill_rate[]"  class="form-control bill-rate" autocomplete="off"><option value="500">500</option></select>-->
<input type="number" name="bill_rate[]" autocomplete="off" value="{$data->bill_rate}" class="form-control bill-rate" autocomplete="off" />
HTML;
                return $html;
            })
            /*->addColumn('income_tax', function ($data) {
                $status = ($data->tin == null) ? 'selected' : '';
                $html = <<<HTML
<select name="income_tax[]"  class="form-control income-tax" autocomplete="off"><option value="10">10%</option><option value="15" $status>15%</option></select>
HTML;
                return $html;
            })*/
            ->addColumn('income_tax', function ($data) {
                $status = ($data->tin == null) ? 'selected' : '';
                //<select name="income_tax[]"  class="form-control" autocomplete="off"><option value="10">10%</option><option value="15" $status >15%</option></select>
                $html = <<<HTML
<select name="income_tax[]"  class="form-control" autocomplete="off"><option value="10">10%</option></select>
HTML;

                return $html;
            })
            ->addColumn('tax_amount', function ($data) {
                $html = <<<HTML
<input type="number" name="tax_amount[]" readonly autocomplete="off" class="form-control tax-amount" autocomplete="off" /></select>
HTML;
                return $html;
            })
            /*->addColumn('vat', function ($data) {
//                return '15%';
                $html = <<<HTML
                <div class="input-group">
                    <input type="text" name="vat[]"  id="vat" value="15"  class="form-control vat-change" />
                    <div class="input-group-append">
                        <label class="input-group-text" id="vat">%</label>
                    </div>
                </div>
HTML;
                return $html;
            })*/
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
                if(!empty($data->tin)){
                    return $data->tin;
                }else{
                    return 'NO';
                }
            })
            ->addColumn('bill_amount', function ($data) {
               // $val = $data->duration * 500;
                $val = $data->duration * $data->bill_rate;
                $html = <<<HTML
<input type="hidden" name="training_id[]" value="{$data->training_id}" />
<input type="hidden" name="supp_stuff_id[]" value="{$data->supp_stuff_id}" />
<input type="hidden" name="schedule_id[]" value="{$data->schedule_id}" />
<input type="hidden" name="bill_date[]" value="{$data->bill_date}" />
<input type="hidden" name="duration[]" value="{$data->duration}" />
<input type="hidden" name="tin[]" value="{$data->tin}" />
<input type="number" name="bill_amount[]" readonly value="{$val}" class="form-control" autocomplete="off" />
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
            foreach ($request->get('supp_stuff_id') as $indx => $value) {
                $supp_stuff_id = null;
                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");
                $bill_amount = $request->get('bill_amount')[$indx];

                $vat = $request->get('vat')[$indx];
                $vat_amount = $bill_amount * $vat / 100;

                $income_tax =$bill_amount * $request->get('income_tax')[$indx]/100;
                $total_dedaction = $vat_amount + $income_tax;
                $net_amount = $bill_amount - $total_dedaction;

                $params = [
                    "P_PAYEE_BILL_ID" => [
                        'value' => &$supp_stuff_id,
                        'type' => \PDO::PARAM_INPUT_OUTPUT,
                        'length' => 255
                    ],
                    "P_TRAINING_ID" => $request->get('training_id')[$indx],
                    "P_SCHEDULE_ID" => $request->get('schedule_id')[$indx],
                    "P_PAYEE_ID" => $request->get('supp_stuff_id')[$indx],
                    "P_PAYMENT_DATE" => $request->get('bill_date')[$indx],
                    "P_VAT_AMOUNT" => $vat_amount,
                    "P_ITD_AMOUNT" => $income_tax,
                    "P_TOTAL_DEDUCATION" => $total_dedaction,
                    "P_PAYEE_TYPE_ID" => '3',
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
                    $params['html'] = view('training.sstuffbillprep.entry.message')->with('params', $params)->render();
                }

                DB::commit();
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $exception->getMessage()];
        }

        $lastParams['html'] = view('training.sstuffbillprep.entry.message')->with('params', $lastParams)->render();
        return $lastParams;
    }
}
