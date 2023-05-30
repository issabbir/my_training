<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\LTrainer;
use App\Entities\Training\TrainingScheduleDtl;
use App\Entities\Training\TrainingScheduleMaster;
use App\Helpers\HelperClass;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingReScheduleController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        return view('training.trainingreschedule.training_reschedule', [
            'trainingschedule' => TrainingScheduleMaster::with('training_info')->where('schedule_status_id', '!=', '4')->get()
        ]);
    }

    public function scheduleData(Request $request)
    {
        $schedule_id = $request->get("schedule_id");
        $scheduleData = TrainingScheduleMaster::select('training_schedule_mst.coordinator_name AS coordinator_name',
            'training_schedule_mst.training_start_date AS training_start_date',
            'training_schedule_mst.training_end_date AS training_end_date',
            'training_schedule_mst.training_capacity AS training_capacity',
            'training_schedule_mst.re_schedule_start_date AS re_schedule_start_date',
            'training_schedule_mst.re_schedule_end_date AS re_schedule_end_date',
            'training_schedule_mst.re_schedule_start_time AS re_schedule_start_time',
            'training_schedule_mst.re_schedule_end_time AS re_schedule_end_time',
            'training_schedule_mst.postponed_date AS postponed_date',
            'training_calender_mst.calender_name AS calender_name',
            'l_training_location.location_name AS location_name')
            ->leftJoin('training_calender_mst', 'training_calender_mst.calender_id', '=', 'training_schedule_mst.calender_id')
            ->leftJoin('l_training_location', 'l_training_location.location_id', '=', 'training_schedule_mst.location_id')
            ->where('training_schedule_mst.schedule_id', '=', $schedule_id)->first();

        $scheduleData->re_schedule_start_time = HelperClass::customTimeFormat($scheduleData->re_schedule_start_time);
        $scheduleData->re_schedule_end_time = HelperClass::customTimeFormat($scheduleData->re_schedule_end_time);

        return $scheduleData;
    }

    public function post(Request $request)
    {
        $response = $this->training_reschedule_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-re-schedule.training-re-schedule-index');
    }


    private function training_reschedule_api_ins(Request $request)
    {
//dd($request);
        try {
            $id = $request->get('schedule_id');
            $start_date = $request->get('from_date-re');
            $end_date = $request->get('to_date-re');
            $timefrom = $request->get('time_from');
            $timeto = $request->get('time_to');
            $postphone_date = $request->get('postphone_date');

            if ($start_date != '') {
                $fromDate = isset($start_date) ? date('Y-m-d', strtotime($start_date)) : '';
                $toDate = isset($end_date) ? date('Y-m-d', strtotime($end_date)) : '';

                $startTime = isset($timefrom) ? $fromDate . ' ' . date('H:i:s', strtotime($timefrom)) : '';
                $endTime = isset($timeto) ? $fromDate . ' ' . date('H:i:s', strtotime($timeto)) : '';
            } else {
                $querys = "SELECT * FROM TRAINING_SCHEDULE_MST WHERE SCHEDULE_ID = $id";

                $ScheduleMaster = DB::select(DB::raw($querys));
                $fromDate = isset($ScheduleMaster[0]->re_schedule_start_date) ? date('Y-m-d', strtotime($ScheduleMaster[0]->re_schedule_start_date)) : '';
                $toDate = isset($ScheduleMaster[0]->re_schedule_end_date) ? date('Y-m-d', strtotime($ScheduleMaster[0]->re_schedule_end_date)) : '';

                $startTime = isset($ScheduleMaster[0]->re_schedule_start_time) ? $fromDate . ' ' . date('H:i:s', strtotime($ScheduleMaster[0]->re_schedule_start_time)) : '';
                $endTime = isset($ScheduleMaster[0]->re_schedule_end_time) ? $fromDate . ' ' . date('H:i:s', strtotime($ScheduleMaster[0]->re_schedule_end_time)) : '';

            }
            $postponeDate = isset($postphone_date) ? date('Y-m-d', strtotime($postphone_date)) : '';

            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");
            $params = [
                "P_SCHEDULE_ID" => $request->get('schedule_id'),
                "P_BATCH_ID" => $request->get('batch_id'),
                "P_SCHEDULE_STATUS_ID" => $request->get('schedule_status'),
                "P_RE_SCHEDULE_START_DATE" => $fromDate,
                "P_RE_SCHEDULE_END_DATE" => $toDate,
                "P_RE_SCHEDULE_START_TIME" => $startTime,
                "P_RE_SCHEDULE_END_TIME" => $endTime,
                "P_POSTPONED_DATE" => $postponeDate,
                "P_INSERT_BY" => auth()->id(),
                "o_status_code" => &$status_code,
                "o_status_message" => &$status_message
            ];

            DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.RE_SCHEDULE_MST_UPD_PR", $params);
            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            if ($request->get('schedule_dtl_id')) {

                foreach ($request->get('schedule_dtl_id') as $indx => $value) {

                    $ScheduleDtl = TrainingScheduleDtl::where('schedule_dtl_id', $request->get('schedule_dtl_id')[$indx])->first();
                    if($ScheduleDtl->re_schedule_date!=null){
                        $db_re_schedule_date = date('Y-m-d', strtotime($ScheduleDtl->re_schedule_date));
                    }else{
                        $db_re_schedule_date = null;
                    }

                    if($ScheduleDtl->re_schedule_start_time!=null){
                        $db_re_schedule_start_time = $fromDate . ' ' .date('H:i:s', strtotime($ScheduleDtl->re_schedule_start_time));
                    }else{
                        $db_re_schedule_start_time = null;
                    }

                    if($ScheduleDtl->re_schedule_end_time!=null){
                        $db_re_schedule_end_time = $fromDate . ' ' .date('H:i:s', strtotime($ScheduleDtl->re_schedule_end_time));
                    }else{
                        $db_re_schedule_end_time = null;
                    }
                    $reschedule_date = isset($request->get('reschedule_date')[$indx]) ? date('Y-m-d', strtotime($request->get('reschedule_date')[$indx])) : $db_re_schedule_date;
                    $startTime = isset($request->get('in_time')[$indx]) ? $reschedule_date . ' ' . (date('H:i:s', strtotime($request->get('in_time')[$indx]))) : $db_re_schedule_start_time;
                    $endTime = isset($request->get('out_time')[$indx]) ? $reschedule_date . ' ' . (date('H:i:s', strtotime($request->get('out_time')[$indx]))) : $db_re_schedule_end_time;

                    if($request->get('reschedule_trainer')[$indx] != null)
                    {
                        $reschedule_trainer = $request->get('reschedule_trainer')[$indx];
                    }
                    else
                    {
                        $ScheduleDtl = TrainingScheduleDtl::where('schedule_dtl_id', $request->get('schedule_dtl_id')[$indx])->first();
                        $reschedule_trainer = $ScheduleDtl->trainer_id;
                    }

                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_SCHEDULE_ID" => $request->get('schedule_id'),
                        "P_SCHEDULE_DTL_ID" => $request->get('schedule_dtl_id')[$indx],
                        "P_SCHEDULE_STATUS_ID" => $request->get('schedule_status'),
                        "P_RE_SCHEDULE_DATE" => $reschedule_date,
                        "P_RE_SCHEDULE_START_TIME" => $startTime,
                        "P_RE_SCHEDULE_END_TIME" => $endTime,
                        "P_TRAINER_ID" => $reschedule_trainer,
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];

                    DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.RE_SCHEDULE_DTL_UPD_PR", $params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }


                }
            }

            if ($request->get('schedule_status') == '1') {
                $querys = "SELECT
                    ('0' || TLT.MOBILE_NO)            AS MOBILE,
                (TLT.EMAIL_ADD)                   AS EMAIL
            FROM TIMS.TRAINING_SCHEDULE_MST        TTSM,
                   TIMS.TRAINING_SCHEDULE_DTL        TTSD,
                   TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
                   TIMS.L_TRAINER                    TLT
             WHERE     TTSM.SCHEDULE_ID = TTSD.SCHEDULE_ID
                AND TTSM.SCHEDULE_ID = TTAS.SCHEDULE_MST_ID
                AND TTSD.TRAINER_ID = TLT.TRAINER_ID
                AND TTSM.SCHEDULE_ID = '" . $request->get("schedule_id") . "'
            UNION SELECT
                ('0' || TT.CELL_NUMBER)            AS MOBILE,
               (TT.EMAIL)                         AS EMAIL
            FROM TIMS.TRAINING_SCHEDULE_MST        TTSM,
                   TIMS.TRAINING_SCHEDULE_DTL        TTSD,
                   TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
                   TIMS.TRAINEE                      TT
             WHERE     TTSM.SCHEDULE_ID = TTSD.SCHEDULE_ID
                AND TTSM.SCHEDULE_ID = TTAS.SCHEDULE_MST_ID
                AND TTAS.TRAINEE_ID = TT.TRAINEE_ID
                AND TTSM.SCHEDULE_ID = '" . $request->get("schedule_id") . "'";
                $contactDetail = DB::select(DB::raw($querys));
                foreach ($contactDetail as $indx => $value) {
                    $msg_bdy = 'Your Schedule date changed.';
                    $email_sub = 'Reschedule Information';
                    $sender_email = 'sabbir.cse.uiu@gmail.com';
                    $email_body = '';
                    $trace_code = rand(99, 9999);
                    $receiver_email = '["' . $contactDetail[$indx]->email . '"]';
                    $result_sms = $this->sendSMS($contactDetail[$indx]->mobile, $msg_bdy, $trace_code);
                    $result_email = $this->sendEmail($trace_code, $email_sub, $sender_email, $receiver_email, $email_body);
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        DB::commit();
        return $params;

    }


    public function scheduleDtlData(Request $request)
    {
        $schedule_id = $request->get("schedule_id");
        $scheduleData = TrainingScheduleDtl::with('l_trainer_info')->where('schedule_id', '=', $schedule_id)->get();
        return datatables()->of($scheduleData)
            ->addColumn('trainer_name', function ($data) {
                if($data->l_trainer_info)
                {
                    return $data->l_trainer_info->trainer_name;
                }
                return '';
            })
            ->addColumn('training_date', function ($data) {
                return Carbon::parse($data->training_date)->format('Y-m-d');
            })
            ->addColumn('reschedule_trainer', function ($data) {
                $html = <<<HTML
<div class="row"><div class="col">
<select class="custom-select form-control select2" id="reschedule_trainer_$data->schedule_dtl_id " name="reschedule_trainer[]">
                                                <option value="">Select One</option>
HTML;
                $trainerlist = LTrainer::all();
                foreach($trainerlist as $value) {
                    $html = $html . '
                    <option
                        value="'.$value->trainer_id.'">'.$value->trainer_name.'</option>';
                }

                $html = $html . '</select></div></div>';

                return $html;
            })
            ->addColumn('reschedule_date', function ($data) {
                $html = <<<HTML
<div class="row"><div class="col">
<input type="text" onclick="call_date_picker(this)" autocomplete="off" class="form-control datetimepicker-input"
    id="reschedule_date_$data->schedule_dtl_id" data-target="#reschedule_date_$data->schedule_dtl_id"
    data-toggle="datetimepicker" name="reschedule_date[]"
/></div></div>
HTML;
                return $html;
            })
            ->addColumn('in_time', function ($data) {
                $html = <<<HTML
<div class="row"><div class="col">
<input type="text" onclick="call_time_picker(this)" autocomplete="off" class="form-control datetimepicker-input"
    id="in_time_$data->schedule_dtl_id" data-target="#in_time_$data->schedule_dtl_id"
    data-toggle="datetimepicker" name="in_time[]"
/></div></div>
HTML;
                return $html;
            })
            ->addColumn('out_time', function ($data) {
                $html = <<<HTML
<div class="row"><div class="col">
<input type="hidden" name="schedule_dtl_id[]" value="{$data->schedule_dtl_id}" />
<input type="text" onclick="call_time_picker2(this)" autocomplete="off" class="form-control datetimepicker-input"
    id="out_time_$data->schedule_dtl_id" data-target="#out_time_$data->schedule_dtl_id"
    data-toggle="datetimepicker" name="out_time[]"
/></div></div>
HTML;
                return $html;
            })
            ->rawColumns(['reschedule_trainer','reschedule_date', 'in_time', 'out_time'])
            ->addIndexColumn()
            ->make(true);

    }

    public function rescheduleDtlData(Request $request)
    {
        $schedule_id = $request->get("schedule_id");
        $scheduleData = TrainingScheduleDtl::with('l_trainer_info', 'prev_trainer_info')->where('schedule_id', '=', $schedule_id)->get();

        return datatables()->of($scheduleData)
            ->addColumn('trainer_name', function ($data) {
                if($data->l_trainer_info)
                {
                    return $data->l_trainer_info->trainer_name;
                }
                return '';
            })
            ->addColumn('training_date', function ($data) {
                return Carbon::parse($data->training_date)->format('Y-m-d');
            })
            ->addColumn('re_schedule_trainer', function ($data) {
                if($data->prev_trainer_info)
                {
                    return $data->prev_trainer_info->trainer_name;
                }
                return '';
            })
            ->addColumn('re_schedule_date', function ($data) {
                if($data->re_schedule_date!=null){
                    return Carbon::parse($data->re_schedule_date)->format('Y-m-d');
                }else{
                    return '--';
                }
            })
            ->addColumn('re_schedule_start_time', function ($data) {
                if($data->re_schedule_date!=null){
                    return HelperClass::customTimeFormat($data->re_schedule_start_time);
                }else{
                    return '--';
                }
            })
            ->addColumn('re_schedule_end_time', function ($data) {
                if($data->re_schedule_date!=null){
                    return HelperClass::customTimeFormat($data->re_schedule_end_time);
                }else{
                    return '--';
                }
            })
            ->addIndexColumn()
            ->make(true);

    }

    public function sendSMS($sender_no, $msg_bdy, $trace_code)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://192.168.78.10:5123/api/v1/client/sms-request",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('msisdn' => $sender_no, 'msg' => $msg_bdy, 'service' => '3', 'trace_code' => $trace_code),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer rZK0hN5unNzkowe4FrRcmOS7fhvWBrSnjqAm3OXnkjQ7aMpGLY51mpxJ7avnhCj9fyTbFK0AidkKitcq"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $json = json_decode($response, true);
        if ($json['code']) {
            if ($json['code'] == "000") {
                $result = '1';
            } else {
                $result = '0';
            }
        } else {
            $result = '0';
        }

        return $result;
    }

    public function sendEmail($trace_code, $email_sub, $sender_email, $receiver_email, $email_body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://192.168.78.10:5123/api/v1/client/email-request",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('service' => '19', 'trace_code' => $trace_code, 'sub' => $email_sub, 'from' => $sender_email, 'to' => $receiver_email, 'cc' => '[]', 'content_type' => 'HTML', 'body' => '<h1>this is a heml h1</h1>', 'file_url' => '["https://file-examples.com/wp-content/uploads/2017/02/file-sample_100kB.doc","https://file-examples.com/wp-content/uploads/2017/02/file-sample_1MB.doc"]'),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer rZK0hN5unNzkowe4FrRcmOS7fhvWBrSnjqAm3OXnkjQ7aMpGLY51mpxJ7avnhCj9fyTbFK0AidkKitcq"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $json = json_decode($response, true);
        if ($json['code']) {
            if ($json['code'] == "000") {
                $result = '1';
            } else {
                $result = '0';
            }
        } else {
            $result = '0';
        }

        return $json;
    }
}
