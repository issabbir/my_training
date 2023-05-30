<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\TrainingScheduleDtl;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Managers\TrainingManager;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TraineeAttendanceUpdateController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
//        $querys = "SELECT TSD.schedule_dtl_id     AS schedule_dtl_id,
//       TSM.BATCH_ID            AS batch_id,
//       --TSD.TRAINING_DATE       AS attendance_date
//       (CASE
//          WHEN TSD.RE_SCHEDULE_DATE IS NULL THEN TSD.TRAINING_DATE
//          ELSE TSD.RE_SCHEDULE_DATE
//        END) attendance_date
//  FROM TRAINING_SCHEDULE_DTL TSD,
//       TRAINING_SCHEDULE_MST TSM
// WHERE TSM.SCHEDULE_ID = TSD.SCHEDULE_ID
//     AND TSM.SCHEDULE_STATUS_ID <> 4";
        $querys = "select SCHEDULE_ID,BATCH_ID,COORDINATOR_NAME,TRAINING_ID from TRAINING_SCHEDULE_MST WHERE SCHEDULE_STATUS_ID <> 4";
        $trainingschedule = DB::select(DB::raw($querys));
        return view('training.attendance.update.attendance', [
            'trainingschedule' => $trainingschedule
        ]);
    }

    public function dataTableList(Request $request)
    {
//        dd($request);
//        $date = $request->get("exam_date");
//        $examDate = isset($date) ? date('Y-m-d', strtotime($date)) : '';

        $stringParts = explode("||||", $request->get("batch_id"));
        $batch_id = $stringParts[0];

        $total_trainee = $this->trainingManager->getTotalTrainee($batch_id);
        $traineeinfo = $this->trainingManager->attendanceUpdateSearch($batch_id);

        return datatables()->of($total_trainee)
            ->addColumn('selected', function ($data) use ($traineeinfo) {
                $html = '';
                $date_chk = TrainingScheduleDtl::where('schedule_id', $data->schedule_id)->distinct('training_date')->orderBy('training_date', 'ASC')->pluck('training_date')->toArray();//dd($date_chk);
                $str = implode(", ", $date_chk);
                foreach ($traineeinfo as $info) {
                    $date = date('Y-m-d', strtotime($info->attendance_date)) . ' 00:00:00';
                    if (str_contains($str, $date)) {
                        if ($info->trainee_id == $data->trainee_id) {
                            $status = ($info->attendance_yn == YesNoFlag::YES) ? 'checked' : '';
                            $value = YesNoFlag::YES;
                            $html = $html . '<input type="hidden" name="attendance_id[' . $data->trainee_id . '][' . $info->attendance_date . ']" value="' . $info->attendance_id . '" />';
                            $html = $html . '<div class="d-inline-block col-md-2"><input style="margin-right:4px;" type="checkbox" id="selected[' . $info->attendance_date . '][' . $info->trainee_id . ']" name="selected[' . $info->attendance_date . '][' . $info->trainee_id . ']" value="' . $value . '" ' . $status . ' />';
                            $html = $html . '<label class="pr-1" for="selected[' . $info->attendance_date . '][' . $info->trainee_id . ']">' . date('d-M-y', strtotime($info->attendance_date)) . '</label></div>';
                        }
                    }
                }
                $html = $html . '<input type="hidden" name="schedule_id" value="' . $data->schedule_id . '" />
<input type="hidden" name="batch_id" value="' . $data->batch_id . '" />
<input type="hidden" name="trainee_id[]" value="' . $data->trainee_id . '" />
<input type="hidden" name="student[' . $data->trainee_id . ']" value="' . $data->student . '" />';
//                foreach($traineeinfo as $info) {
//                    $html = $html.'<input type="hidden" name="dates[]" value="'.$info->attendance_date.'" />';
//                }

                return $html;
            })->rawColumns(['selected'])
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
        $lastParams = [];
        DB::beginTransaction();
        try {
            $traineeinfo = $this->trainingManager->attendanceUpdateSearch($request->get('batch_id'));

            $date_chk = TrainingScheduleDtl::where('schedule_id', $traineeinfo[0]->schedule_mst_id)->distinct('training_date')->orderBy('training_date', 'ASC')->pluck('training_date')->toArray();//dd($date_chk);
            $str = implode(", ", $date_chk);
            foreach ($traineeinfo as $tInfo) {
                $date = $tInfo->attendance_date;
                $dateck = date('Y-m-d', strtotime($date)) . ' 00:00:00';
                if (str_contains($str, $dateck)) {
                    $attDate = isset($date) ? date('Y-m-d', strtotime($date)) : '';
                    foreach ($request->get("trainee_id") as $key => $trainee_id) {
                        if ($request->has("selected")) {
                            $day_exist = array_key_exists($date, $request->get("selected"));
                            if ($day_exist) {
                                $id_exist = array_key_exists($trainee_id, $request->get("selected")[$date]);
                                if ($id_exist) {
                                    $attended = 'Y';
                                } else {
                                    $attended = 'N';
                                }
                            } else {
                                $attended = 'N';
                            }
                        } else {
                            $attended = 'N';
                        }
                        $params = [];

                        $status_code = sprintf("%4000s", "");
                        $O_ATT_ID = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params = [
                            "P_ATTENDANCE_ID" => $request->get('attendance_id')[$trainee_id][$date],
                            "P_ATTENDANCE_DATE" => $attDate,
                            "P_TRAINEE_ID" => $trainee_id,
                            "P_TRAINEE_NAME" => $request->get('student')[$trainee_id],
                            "P_SCHEDULE_MST_ID" => $request->get('schedule_id'),
                            "P_BATCH_ID" => $request->get('batch_id'),
                            "P_IN_TIME" => '',
                            "P_OUT_TIME" => '',
                            "P_TRAINING_HOURS" => '',
                            "P_REMARKS" => '',
                            "P_ATTENDANCE_YN" => $attended,
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "O_ATT_ID" => &$O_ATT_ID,
                            "o_status_message" => &$status_message
                        ];

                        DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_ATTENDANCE_UPD_PR", $params);
                        $lastParams = $params;

                        if ($params['o_status_code'] != 1) {
                            DB::rollBack();
                            $params['html'] = view('training.attendance.update.message')->with('params', $params)->render();
                        }

                    }
                }else{
                    continue;
                }

            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $exception->getMessage()];
        }

        $lastParams['html'] = view('training.attendance.update.message')->with('params', $lastParams)->render();
        return $lastParams;
    }

}
