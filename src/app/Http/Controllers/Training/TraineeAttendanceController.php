<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\TrainingScheduleDtl;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Managers\TrainingManager;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraineeAttendanceController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
        $querys = "select SCHEDULE_ID,BATCH_ID,COORDINATOR_NAME,TRAINING_ID from TRAINING_SCHEDULE_MST";
        $trainingschedule = DB::select(DB::raw($querys));

        return view('training.attendance.entry.attendance', [
            'trainingschedule' => $trainingschedule
        ]);
    }

    public function dataTableList(Request $request)
    {
        $stringParts = explode("||||", $request->get("batch_id"));
        $batch_id = $stringParts[0];

        $total_trainee = $this->trainingManager->getTotalTrainee($batch_id);
        $traineeinfo = $this->trainingManager->attendanceUpdateSearch($batch_id);
//        dd($traineeinfo);
        if ($traineeinfo)//For update
        {
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
                    <input type="hidden" name="update" value="' . '1' . '" />
<input type="hidden" name="batch_id" value="' . $data->batch_id . '" />
<input type="hidden" name="trainee_id[]" value="' . $data->trainee_id . '" />
<input type="hidden" name="student[' . $data->trainee_id . ']" value="' . $data->student . '" />';
                    foreach ($traineeinfo as $info) {
                        $html = $html . '<input type="hidden" name="dates[]" value="' . $info->attendance_date . '" />';
                    }

                    return $html;
                })->rawColumns(['selected'])
                ->addIndexColumn()
                ->make(true);
        } else {//For New Entry

            if (!empty($request->get("batch_id"))) {

                $stringParts = explode("||||", $request->get("batch_id"));
                $batch_id = $stringParts[0];
                $schedule_id = $stringParts[1];

                $traineeinfo = $this->trainingManager->attendanceEntrySearch($batch_id);
                $total_days = $this->trainingManager->totalDays($schedule_id);

                $date_range = $this->createDateRangeArray($total_days->training_start_date, $total_days->training_end_date);

                return datatables()->of($traineeinfo)
                    ->addColumn('selected', function ($data) use ($date_range) {
                        $html = '';
                        $date_chk = TrainingScheduleDtl::where('schedule_id', $data->schedule_id)->distinct('training_date')->orderBy('training_date', 'ASC')->pluck('training_date')->toArray();//dd($date_chk);
                        $str = implode(", ", $date_chk);
//                        dd($str);

                        foreach ($date_range as $value) {
                            $date = date('Y-m-d', strtotime($value)) . ' 00:00:00';
                            if (str_contains($str, $date)) {
                                $html = $html . '<div class="d-inline-block col-md-2"><input style="margin-right:4px;" type="checkbox" id="selected[' . $value . '][' . $data->trainee_id . ']" name="selected[' . $value . '][' . $data->trainee_id . ']" value="Y" checked/>';
                                $html = $html . '<label class="pr-1" for="selected[' . $value . '][' . $data->trainee_id . ']">' . date('d-M-y', strtotime($value)) . '</label></div>';
                            }
                        }

                        $html = $html . '<input type="hidden" name="schedule_id" value="' . $data->schedule_id . '" />
<input type="hidden" name="batch_id" value="' . $data->batch_id . '" />
<input type="hidden" name="trainee_id[]" value="' . $data->trainee_id . '" />
<input type="hidden" name="student[' . $data->trainee_id . ']" value="' . $data->student . '" />';
                        foreach ($date_range as $value) {
                            $date = date('Y-m-d', strtotime($value)) . ' 00:00:00';
                            if (str_contains($str, $date)) {
                                $html = $html . '<input type="hidden" name="dates[' . $data->trainee_id . '][]" value="' . $value . '" />';
                            }
                        }
                        return $html;
                    })->rawColumns(['selected'])
                    ->addIndexColumn()
                    ->make(true);
            }
            $traineeinfo = $this->trainingManager->attendanceEntrySearch($request->get("batch_id"));
            return datatables()->of($traineeinfo)
                ->make(true);
        }
    }

    private function createDateRangeArray($strDateFrom, $strDateTo)
    {
        $aryRange = [];

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    public function post(Request $request)
    {//dd($request->all());
        if ($request->update) {
            $lastParams = [
                'o_status_code' => '99',
                'o_status_message' => 'SORRY! ATTENDANCE RECORD ALREADY EXIST!'
            ];
        } else {
//dd($request);
            $dates = $request->get('dates')[$request->get('trainee_id')[0]];
            $lastParams = [];
            //dd($request);
            DB::beginTransaction();

            try {
                foreach ($dates as $date) {
                    $attDate = isset($date) ? date('Y-m-d', strtotime($date)) : '';
                    foreach ($request->get("trainee_id") as $key => $trainee_id) {
                        $attended = 'N';
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
                        $attendance_id = null;
                        $status_code = sprintf("%4000s", "");
                        $O_ATT_ID = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params = [
                            "P_ATTENDANCE_ID" => $attendance_id,
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
                            "O_ATT_ID" => $O_ATT_ID,
                            "o_status_message" => &$status_message
                        ];

                        DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_ATTENDANCE_ENTRY_PR", $params);

                        $lastParams = $params;

                        if ($params['o_status_code'] != 1) {
                            DB::rollBack();
                            $params['html'] = view('training.attendance.entry.message')->with('params', $params)->render();
                        }

                        $params = [];

                    }
                }
            } catch (\Exception $exception) {
                DB::rollBack();
                return ["exception" => true, "o_status_code" => false, "o_status_message" => $exception->getMessage()];
            }
        }
        DB::commit();

        $lastParams['html'] = view('training.attendance.entry.message')->with('params', $lastParams)->render();
        return $lastParams;
    }
}
