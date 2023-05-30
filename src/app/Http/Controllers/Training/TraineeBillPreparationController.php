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

class TraineeBillPreparationController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
        return view('training.traineebillprep.entry.trainee_bill_preparation', [
            'trainingInfo' => TrainingInfo::all()
        ]);
    }

    public function datatable(Request $request)
    {
//dd($request);
        $training_no = $request->get("training_no");
        $schedule_id = $request->get("schedule_id");
//        $training_duration = $request->get("training_duration");
//        $bill_date = $request->get("bill_date");

        $query = <<<QUERY
 SELECT DISTINCT
       tt.trainee_id
           AS trainee_id,
       0 as emp_type_id,
       tsm.schedule_id
           AS schedule_id,
       tsm.training_id
           AS training_id,
       ('(' || ttif.training_number || ') ' || ttif.training_title)
           training_info,
       tt.trainee_name
           AS trainee_name,
       tt.organization_name
           AS organization_name,
       tt.designation_name
           AS desig_name,
       tt.department_name
           AS dept_name,
           COUNT (TA.ATTENDANCE_ID) OVER (PARTITION BY TA.TRAINEE_ID) AS TOT_DAYS
  FROM tims.trainee_assignment_schedule  ttas,
       tims.trainee_attendance           ta,
       tims.trainee                      tt,
       tims.training_schedule_mst        tsm,
       tims.training_info                ttif
 WHERE     tt.trainee_id = ta.trainee_id
       AND ttas.trainee_id = ta.trainee_id
       AND ta.attendance_yn = 'Y'
       AND tsm.schedule_id = ttas.schedule_mst_id
       AND tsm.schedule_id = ta.schedule_mst_id
       AND ttif.training_id = tsm.training_id
       AND ttif.training_id = :p_training_id
       AND TA.SCHEDULE_MST_ID = :p_schedule_id
 UNION ALL
 SELECT DISTINCT
       pe.emp_id
           AS trainee_id,
       pe.emp_type_id  as emp_type_id,
       tsm.schedule_id
           AS schedule_id,
       tsm.training_id
           AS training_id,
       ('(' || ttif.training_number || ') ' || ttif.training_title)
           training_info,
       pe.emp_name
           AS trainee_name,
       'CPA'
           AS organization_name,
       plde.designation
           AS desig_name,
       pld.department_name
           AS dept_name,
           COUNT (TA.ATTENDANCE_ID) OVER (PARTITION BY TA.TRAINEE_ID) AS TOT_DAYS
  FROM tims.trainee_assignment_schedule  ttas,
       tims.trainee_attendance           ta,
       pmis.employee                     pe,
       tims.training_schedule_mst        tsm,
       tims.training_info                ttif,
       pmis.l_department                 pld,
       pmis.l_designation                plde
 WHERE     pe.emp_id = ta.trainee_id
       AND ta.trainee_id = ttas.trainee_id
       AND ta.attendance_yn = 'Y'
       AND tsm.schedule_id = ttas.schedule_mst_id
       AND tsm.schedule_id = ta.schedule_mst_id
       AND ttif.training_id = tsm.training_id
       AND pe.dpt_department_id = pld.department_id
       AND pe.designation_id = plde.designation_id
       AND ttif.training_id = :p_training_id
       AND TA.SCHEDULE_MST_ID = :p_schedule_id
QUERY;



//        $billinfo = DB::select($query, ['p_training_id' => $training_no, 'p_schedule_id' => $schedule_id, 'p_duration' => $training_duration, 'p_bill_date' => $bill_date]);
        $billinfo = DB::select($query, ['p_training_id' => $training_no, 'p_schedule_id' => $schedule_id]);

        return datatables()->of($billinfo)
            ->addColumn('bill_rate', function ($data) {

                if ($data->emp_type_id == 1) { // 1 = officer
                    $html = <<<HTML
                <select name="bill_rate[]"  class="form-control bill-rate" autocomplete="off">
                    <option value="600" selected>600</option>
<!--                    <option value="500">500</option>-->
                </select>

HTML;
                }
                else { //else staff
                    $html = <<<HTML
                <select name="bill_rate[]"  class="form-control bill-rate" autocomplete="off">
<!--                    <option value="600" >600</option>-->
                    <option value="500" selected>500</option>
                </select>

HTML;
                }
                return $html;
            })
            ->addColumn('bill_amount', function ($data) {
                    if ($data->emp_type_id == 1) // 1 = officer
                        $amount = $data->tot_days * 600;
                    else
                        $amount = $data->tot_days * 500;
                    $html = <<<HTML
<input type="hidden" name="training_id[]" value="{$data->training_id}" />
<input type="hidden" name="schedule_id[]" value="{$data->schedule_id}" />
<input type="hidden" name="trainee_id[]" value="{$data->trainee_id}" />
<input type="hidden" name="tot_days[]" value="{$data->tot_days}" />

<input type="number" name="bill_amount[]" value="{$amount}" readonly class="form-control" autocomplete="off" />
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
                $trainee_bill_id = null;
                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");
                //$att_id = sprintf("%4000s", "");
                $params = [
                    "P_PAYEE_BILL_ID" => [
                        'value' => &$trainee_bill_id,
                        'type' => \PDO::PARAM_INPUT_OUTPUT,
                        'length' => 255
                    ],
                    "P_TRAINING_ID" => $request->get('training_id')[$indx],
                    "P_SCHEDULE_ID" => $request->get('schedule_id')[$indx],
                    "P_PAYEE_ID" => $request->get('trainee_id')[$indx],
//                                "P_PAYMENT_DATE" => $request->get('bill_date')[$indx],
                    "P_VAT_AMOUNT" => '0',
                    "P_ITD_AMOUNT" => '0',
                    "P_TOTAL_DEDUCATION" => '0',
                    "P_Payee_Type_ID" => '1',
                    "P_DURATION" => $request->get('tot_days')[$indx],
                    "P_RATE" => $request->get('bill_rate')[$indx],
                    "P_TOTAL_AMOUNT" => $request->get('bill_amount')[$indx],
                    "P_NET_AMOUNT" => '0',
                    "P_REMARKS" => '',
                    "P_APPROVED_YN" => '',
                    "P_INSERT_BY" => auth()->id(),
                    //"O_ATT_ID" => &$att_id,
                    "o_status_code" => &$status_code,
                    "o_status_message" => &$status_message
                ];

                DB::executeProcedure("TIMS.TRAINING_APPROVAL_PKG.BILL_INFO_PR", $params);

                $lastParams = $params;

                if ($params['o_status_code'] != 1) {
                    DB::rollBack();
                    $params['html'] = view('training.traineebillprep.entry.message')->with('params', $params)->render();
                }

                DB::commit();
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $exception->getMessage()];
        }

        $lastParams['html'] = view('training.traineebillprep.entry.message')->with('params', $lastParams)->render();
        return $lastParams;
    }
}
