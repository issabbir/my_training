<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\LExamType;
use App\Entities\Training\LTrainer;
use App\Entities\Training\LTrainingLocation;
use App\Entities\Training\ScheduleSupportInfo;
use App\Entities\Training\TraineeAssignmentSchedule;
use App\Entities\Training\TraineeAttendance;
use App\Entities\Training\TraineeEvaluationMaster;
use App\Entities\Training\TraineeExamSchedule;
use App\Entities\Training\TrainingCalenderMaster;
use App\Entities\Training\TrainingInfo;
use App\Entities\Training\TrainingScheduleDtl;
use App\Entities\Training\TrainingScheduleMaster;
use App\Enums\ScheduleStatus;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Managers\TrainingManager;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingScheduleController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {
//        dd(TrainingCalenderMaster::where('start_date', '<=', Carbon::now())
//            ->where('end_date', '>=', Carbon::now())
//            ->where('active_yn', 'Y')
//            ->get());train
        $trainerlist = DB::select('SELECT t.*,
  CASE
    WHEN t.INTERNAL_YN = \'Y\' THEN e.EMP_CODE
    ELSE \'External\'
  END AS code
FROM tims.l_trainer t
LEFT JOIN PMIS.EMPLOYEE e ON t.emp_id = e.emp_id');

        return view('training.trainingschedule.training_schedule', [
            'trainingschedule' => null,
            'trainingscheduledtl' => null,
            'trainingidlist' => TrainingInfo::all(),
            'traineeList' => $this->trainingManager->traineeList(),
            //'trainerlist' => LTrainer::with('empinfo')->,
            'trainerlist' => $trainerlist,
            'examType' => LExamType::all(),
            'lTrainingLocation' => LTrainingLocation::all(),
//            'trainingcalender' => TrainingCalenderMaster::all()
            'trainingcalender' => TrainingCalenderMaster::where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->where('active_yn', 'Y')
                ->first()
        ]);
    }

    public function edit(Request $request, $id)
    {
        $trainingschedule = TrainingScheduleMaster::select('*')
            ->where('schedule_id', '=', $id)
            ->first();
        $drictor = DB::selectOne("select emp.emp_name, emp.emp_code from pmis.employee emp
where emp.emp_id = :emp_id", ['emp_id' => $trainingschedule->course_director_id]);

        $trainingscheduledtl = TrainingScheduleDtl::where('schedule_id', '=', $id)
            ->orderBy('training_date','ASC')
            ->orderBy('training_start_time','ASC')
            ->get();
        $trainerlist = DB::select('SELECT t.*,
                                        CASE
                                        WHEN t.INTERNAL_YN = \'Y\' THEN e.EMP_CODE
                                        ELSE \'External\'
                                        END AS code
                                        FROM tims.l_trainer t
                                        LEFT JOIN PMIS.EMPLOYEE e ON t.emp_id = e.emp_id');

        $TraineeAssignmentSchedule = $this->trainingManager->traineeassignmentscheduleData($id);
        $traineeCount = count($TraineeAssignmentSchedule);
        $allTrainee = TraineeAssignmentSchedule::where('schedule_mst_id', $id)->get(['trainee_id'])->pluck('trainee_id')->toArray();

        $TraineeExamType = TraineeExamSchedule::where('schedule_mst_id', '=', $id)->get();
        $allExamtype = TraineeExamSchedule::where('schedule_mst_id', $id)->get(['exam_type_id'])->pluck('exam_type_id')->toArray();
        $allExamtype_count = count($allExamtype);

        $supprotingStuff = DB::select("select ssi.*, emp.emp_name,emp.emp_code from schedule_support_info ssi
left join  pmis.employee emp on (emp.emp_id = ssi.emp_id)
where ssi.schedule_mst_id = :schedule_mst_id", ['schedule_mst_id' => $id]);

        $allSupprotingStuff = ScheduleSupportInfo::where('schedule_mst_id', $id)->get(['emp_id'])->pluck('emp_id')->toArray();
        $supprotingStuff_count = count($supprotingStuff);

        $traineeAttendance = TraineeAttendance::where('schedule_mst_id',$id)->first();
//        dd($traineeAttendance);
        return view('training.trainingschedule.training_schedule', [
//        return response()->json([
            'trainingschedule' => $trainingschedule,
            'trainingscheduledtl' => $trainingscheduledtl,
            'TraineeExamType' => $TraineeExamType,
            'traineeCount' => $traineeCount,
            'allTrainee' => json_encode($allTrainee),
            'allExamtype_count' => $allExamtype_count,
            'allExamtype' => json_encode($allExamtype),
            'supprotingStuff' => $supprotingStuff,
            'allSupprotingStuff' => json_encode($allSupprotingStuff),
            'supprotingStuff_count' => $supprotingStuff_count,
            'TraineeAssignmentSchedule' => $TraineeAssignmentSchedule,
            'trainingidlist' => TrainingInfo::all(),
            'traineeAttendance' => $traineeAttendance,
//            'trainerlist' => LTrainer::all(),
            'trainerlist' => $trainerlist,
            'examType' => LExamType::all(),
            'drictor' => $drictor,
            'traineeList' => $this->trainingManager->traineeList(),
            'lTrainingLocation' => LTrainingLocation::all(),
//            'trainingcalender' => TrainingCalenderMaster::all()
            'trainingcalender' => TrainingCalenderMaster::where('calender_id', $trainingschedule->calender_id)->first()
        ]);
    }

    public function dataTableList()
    {
        $queryResult = TrainingScheduleMaster::select('training_schedule_mst.coordinator_name AS coordinator_name',
            'l_training_location.location_name AS training_location',
            'training_schedule_mst.batch_id AS batch_id',
            'training_schedule_mst.training_capacity AS training_capacity',
            'training_schedule_mst.training_start_date AS training_start_date',
            'training_schedule_mst.schedule_id AS schedule_id',
            'training_schedule_mst.training_end_date AS training_end_date',
            'training_schedule_mst.schedule_status_id AS schedule_status_id')
            ->leftJoin('l_training_location', 'l_training_location.location_id', '=', 'training_schedule_mst.location_id')
            ->orderBy('training_schedule_mst.insert_date', 'desc')
            ->get();
        return datatables()->of($queryResult)
            ->addColumn('training_start_date', function ($query) {
                return Carbon::parse($query->training_start_date)->format('Y-m-d');
            })
            ->addColumn('training_end_date', function ($query) {
                return Carbon::parse($query->training_end_date)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {

                if($query->schedule_status_id == '4') {
                    $actionBtn = 'Schedule Closed';
                } else {
                    $actionBtn = '<a href="' . route('training-schedule.training-schedule-edit', [$query->schedule_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
                }

                return $actionBtn;

            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
//        dd($request);
        $response = $this->training_schedule_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-schedule.training-schedule-index');
    }

    public function update(Request $request, $id)
    {
        $response = $this->training_schedule_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('training-schedule.training-schedule-index');
    }

    private function training_schedule_api_ins(Request $request)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';
        $disc_amt = isset($postData['disc_amt']) ? $postData['disc_amt'] : '';
        //$scheduleStatus = isset($postData['schedule_status_id']) ? ScheduleStatus::CLOSED : ScheduleStatus::ACTIVE;

        try {
            $training_schedule_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_SCHEDULE_ID' => [
                    'value' => &$training_schedule_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_BATCH_ID' => $postData['batch_id'],
                'P_TRAINING_ID' => $postData['training_id'],
                'P_COORDINATOR_NAME' => $postData['coordinator_name'],
                'P_TRAINING_LOCATION' => '',
                'P_TRAINING_LOCATION_ID' => $postData['training_location'],
                'P_TRAINING_FACALITIES' => '',
                'P_TRAINING_CAPACITY' => $postData['training_capacity'],
                'P_TRAINING_START_DATE' => $fromDate,
                'P_TRAINING_END_DATE' => $toDate,
                'P_TRAINING_TIME_FROM' => '',
                'P_TRAINING_TIME_TO' => '',
                'P_SCHEDULE_STATUS_ID' => ScheduleStatus::ACTIVE,
                'P_TRAINING_FEE' => $postData['training_fee'],
                'P_COORDINATOR_MOBILE' => $postData['mobile_no'],
                'P_COORDINATOR_EMAIL' => $postData['email'],
                'P_REMARKS' => $postData['remarks'],
                'P_ALLOWANCE_YN' => ($postData['allowance_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_CALENDER_ID' => $postData['calender_id'],
                'P_DISCOUNT_YN' => ($postData['discount_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_DISCOUNT_AMOUNT' => $disc_amt,
                'P_TRAINING_TOTAL_COST' => $postData['total_cost'],
                'P_COURSE_DIRECTOR_ID' => $postData['course_director_id'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_SCHEDULE_MST_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }
            if ($request->get('dtl_training_id')) {

                foreach ($request->get('dtl_training_id') as $indx => $value) {

                    $training_date = isset($request->get('training_date')[$indx]) ? date('Y-m-d', strtotime($request->get('training_date')[$indx])) : '';

                    $startTime = isset($request->get('dtl_time_from')[$indx]) ? date('H:i:s', strtotime($request->get('dtl_time_from')[$indx])) : '';
                    $endTime = isset($request->get('dtl_time_to')[$indx]) ? date('H:i:s', strtotime($request->get('dtl_time_to')[$indx])) : '';

                    $pStartTime = $training_date . ' ' . $startTime;
                    $pEndTime = $training_date . ' ' . $endTime;

                    $schedule_dtl_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_SCHEDULE_DTL_ID" => [
                            'value' => &$schedule_dtl_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        "P_SCHEDULE_ID" => $params['P_SCHEDULE_ID'],
                        "P_TRAINING_ID" => '',
                        "P_TRAINER_ID" => $request->get('dtl_trainer_id')[$indx],
                        "P_TRAINING_DATE" => $training_date,
                        "P_TRAINING_START_TIME" => $pStartTime,
                        "P_TRAINING_END_TIME" => $pEndTime,
                        "P_SCHEDULE_STATUS_ID" => '1',
                        "P_ACTIVE_YN" => 'Y',
                        "P_REMARKS" => $request->get('tab_remrks')[$indx],
                        "P_SUBJECT" => $request->get('dtl_subject')[$indx],
                        "P_INSERT_BY" => auth()->id(),
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];

                    DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_SCHEDULE_DTL_PR", $params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }
                }
            }



            if ($request->get('dtl_trainee_id')) {
                foreach ($request->get('dtl_trainee_id') as $indx => $value) {
                    $assignment_id = null;
                    $evaluation_id = null;
                    $trainer_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_ASSIGNMENT_ID" => [
                            'value' => &$assignment_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        "P_SCHEDULE_MST_ID" => $params['P_SCHEDULE_ID'],
                        "P_BATCH_ID" => $request->get('batch_id'),
                        "P_TRAINEE_ID" => $request->get('dtl_trainee_id')[$indx],
                        "P_TRAINEE_NAME" => '',
                        "P_DESIGNATION_ID" => '',
                        "P_DEPARTMENT_ID" => '',
                        "P_ORGANIZATION_NAME" => '',
                        "P_ACTIVE_YN" => '',
                        "P_REMARKS" => '',
                        "P_EVALUATION_ID" => $evaluation_id,
                        "P_TRAINING_ID" => $request->get('training_id'),
                        "P_TRAINER_ID" => $trainer_id,
                        "P_SCHEDULE_STATUS_ID" => '1',
                        "P_INSERT_BY" => auth()->id(),
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];

                    DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_ASSIGNMENT_SCHEDULE_PR", $params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }
                }
            }

            if ($request->get('exam_type_id')) {

                foreach ($request->get('exam_type_id') as $indx => $value) {
                    $trainee_exam_sch_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_TRAINEE_EXAM_SCH_ID" => [
                            'value' => &$trainee_exam_sch_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        "P_SCHEDULE_MST_ID" => $params['P_SCHEDULE_ID'],
                        "P_BATCH_ID" => $request->get('batch_id'),
                        "P_EXAM_TYPE_ID" => $request->get('exam_type_id')[$indx],
                        "P_EXAM_TYPE_NAME" => $request->get('exam_type_name')[$indx],
                        "P_TOTAL_MARKS" => $request->get('total_marks')[$indx],
                        "P_PASS_MARKS" => $request->get('pass_marks')[$indx],
                        "P_ACTIVE_YN" => '',
                        "P_REMARKS" => $request->get('remarks_exam_type')[$indx],
                        "P_INSERT_BY" => auth()->id(),
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];

                    DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_EXAM_SCHEDULE_PR", $params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }
                }
            }

            if ($request->get('emp_id')) {

                foreach ($request->get('emp_id') as $indx => $value) {
                    $support_member_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_SUPPORT_MEMBER_ID" => [
                            'value' => &$support_member_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        "P_SCHEDULE_MST_ID" => $params['P_SCHEDULE_ID'],
                        "P_EMP_ID" => $request->get('emp_id')[$indx],
                        "P_ACTIVE_YN" => 'Y',
                        "P_REMARKS" => '',
                        "P_INSERT_BY" => auth()->id(),
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];

                    DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.SCHEDULE_SUPPORT_INFO_PR", $params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }
                }
            }

        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function training_schedule_api_upd($request, $id)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';
        $disc_amt = isset($postData['disc_amt']) ? $postData['disc_amt'] : '';

        try {
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_SCHEDULE_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_BATCH_ID' => $postData['batch_id'],
                'P_TRAINING_ID' => $postData['training_id'],
                'P_COORDINATOR_NAME' => $postData['coordinator_name'],
                'P_TRAINING_LOCATION' => '',
                'P_TRAINING_LOCATION_ID' => $postData['training_location'],
                'P_TRAINING_FACALITIES' => '',
                'P_TRAINING_CAPACITY' => $postData['training_capacity'],
                'P_TRAINING_START_DATE' => $fromDate,
                'P_TRAINING_END_DATE' => $toDate,
                'P_TRAINING_TIME_FROM' => '',
                'P_TRAINING_TIME_TO' => '',
                'P_SCHEDULE_STATUS_ID' => ($postData['schedule_status_id'] == ScheduleStatus::CLOSED) ? ScheduleStatus::CLOSED : ScheduleStatus::ACTIVE,
                'P_TRAINING_FEE' => $postData['training_fee'],
                'P_COORDINATOR_MOBILE' => $postData['mobile_no'],
                'P_COORDINATOR_EMAIL' => $postData['email'],
                'P_REMARKS' => $postData['remarks'],
                'P_ALLOWANCE_YN' => ($postData['allowance_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_CALENDER_ID' => $postData['calender_id'],
                'P_DISCOUNT_YN' => ($postData['discount_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_DISCOUNT_AMOUNT' => $disc_amt,
                'P_TRAINING_TOTAL_COST' => $postData['total_cost'],
                'P_COURSE_DIRECTOR_ID' => $postData['course_director_id'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_SCHEDULE_MST_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            if (($postData['schedule_status_id'] == ScheduleStatus::ACTIVE)){
                if ($request->get('dtl_trainer_id')) {

                    if ($request->get('schedule_id')) {
                        $schedule_mst_id = $id;
                        TrainingScheduleDtl::where('schedule_id', $schedule_mst_id)->delete();
                    }

                    foreach ($request->get('dtl_trainer_id') as $indx => $value) {
                        $training_date = isset($request->get('training_date')[$indx]) ? date('Y-m-d', strtotime($request->get('training_date')[$indx])) : '';

                        $startTime = isset($request->get('dtl_time_from')[$indx]) ? date('H:i:s', strtotime($request->get('dtl_time_from')[$indx])) : '';
                        $endTime = isset($request->get('dtl_time_to')[$indx]) ? date('H:i:s', strtotime($request->get('dtl_time_to')[$indx])) : '';

                        $pStartTime = $training_date . ' ' . $startTime;
                        $pEndTime = $training_date . ' ' . $endTime;

                        $schedule_dtl_id = null;
                        $status_code = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
//                        if($indx == 2)
//                        {
//                            dd($request);
//                        }
                        $params_dtl = [
                            "P_SCHEDULE_DTL_ID" => [
                                'value' => &$schedule_dtl_id,
                                'type' => \PDO::PARAM_INPUT_OUTPUT,
                                'length' => 255
                            ],
                            "P_SCHEDULE_ID" => $params['P_SCHEDULE_ID'],
                            "P_TRAINING_ID" => '',
                            "P_TRAINER_ID" => $request->get('dtl_trainer_id')[$indx],
                            "P_TRAINING_DATE" => $training_date,
                            "P_TRAINING_START_TIME" => $pStartTime,
                            "P_TRAINING_END_TIME" => $pEndTime,
                            'P_SCHEDULE_STATUS_ID' => $postData['schedule_status_id'],
                            "P_ACTIVE_YN" => 'Y',
                            "P_REMARKS" => $request->get('tab_remrks')[$indx],
                            "P_SUBJECT" => $request->get('subject')[$indx],
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "o_status_message" => &$status_message
                        ];

                        DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_SCHEDULE_DTL_PR", $params_dtl);

                        if ($params_dtl['o_status_code'] != 1) {
                            DB::rollBack();
                            return $params_dtl;
                        }
                    }
                }

                if ($request->get('dtl_trainee_id')) {

                    if ($request->get('dtl_schedule_mst_id')) {
                        $schedule_mst_id = $id;
                        TraineeAssignmentSchedule::where('schedule_mst_id', $schedule_mst_id)->delete();
                        TraineeEvaluationMaster::where('schedule_mst_id', $schedule_mst_id)->delete();
                    }
                    $dtl_trainee_id = array_unique($request->get('dtl_trainee_id'));
                    foreach ($dtl_trainee_id as $indx => $value) {
                        $assignment_id = null;
                        $evaluation_id = null;
                        $trainer_id = null;
                        $status_code = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params_dtl = [
                            "P_ASSIGNMENT_ID" => [
                                'value' => &$assignment_id,
                                'type' => \PDO::PARAM_INPUT_OUTPUT,
                                'length' => 255
                            ],
                            "P_SCHEDULE_MST_ID" => $params['P_SCHEDULE_ID'],
                            "P_BATCH_ID" => $request->get('batch_id'),
                            "P_TRAINEE_ID" => $dtl_trainee_id[$indx],
                            "P_TRAINEE_NAME" => '',
                            "P_DESIGNATION_ID" => '',
                            "P_DEPARTMENT_ID" => '',
                            "P_ORGANIZATION_NAME" => '',
                            "P_ACTIVE_YN" => '',
                            "P_REMARKS" => '',
                            "P_EVALUATION_ID" => $evaluation_id,
                            "P_TRAINING_ID" => $request->get('training_id'),
                            "P_TRAINER_ID" => $trainer_id,
                            "P_SCHEDULE_STATUS_ID" => $postData['schedule_status_id'],
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "o_status_message" => &$status_message
                        ];
                        DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_ASSIGNMENT_SCHEDULE_PR", $params_dtl);
                        if ($params_dtl['o_status_code'] != 1) {
                            DB::rollBack();
                            return $params_dtl;
                        }
                    }
                }

                if ($request->get('exam_type_id')) {

                    if ($request->get('batch_id')) {
                        $delete = $request->get('batch_id');
                        TraineeExamSchedule::where('batch_id', $delete)->delete();
                    }
                    $exam_type_id = array_unique($request->get('exam_type_id'));

                    foreach ($exam_type_id as $indx => $value) {
                        $trainee_exam_sch_id = null;
                        $status_code = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params_dtl = [
                            "P_TRAINEE_EXAM_SCH_ID" => [
                                'value' => &$trainee_exam_sch_id,
                                'type' => \PDO::PARAM_INPUT_OUTPUT,
                                'length' => 255
                            ],
                            "P_SCHEDULE_MST_ID" => $id,
                            "P_BATCH_ID" => $request->get('batch_id'),
                            "P_EXAM_TYPE_ID" => $exam_type_id[$indx],
                            "P_EXAM_TYPE_NAME" => $request->get('exam_type_name')[$indx],
                            "P_TOTAL_MARKS" => $request->get('total_marks')[$indx],
                            "P_PASS_MARKS" => $request->get('pass_marks')[$indx],
                            "P_ACTIVE_YN" => '',
                            "P_REMARKS" => $request->get('remarks_exam_type')[$indx],
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "o_status_message" => &$status_message
                        ];

                        DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.TRAINEE_EXAM_SCHEDULE_PR", $params_dtl);
                        if ($params_dtl['o_status_code'] != 1) {
                            DB::rollBack();
                            return $params_dtl;
                        }
                    }
                }

                if ($request->get('emp_id')) {

                    foreach ($request->get('emp_id') as $indx => $value) {
                        $support_member_id = null;
                        $status_code = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params_dtl = [
                            "P_SUPPORT_MEMBER_ID" => [
                                'value' => &$support_member_id,
                                'type' => \PDO::PARAM_INPUT_OUTPUT,
                                'length' => 255
                            ],
                            "P_SCHEDULE_MST_ID" => $params['P_SCHEDULE_ID'],
                            "P_EMP_ID" => $request->get('emp_id')[$indx],
                            "P_ACTIVE_YN" => 'Y',
                            "P_REMARKS" => '',
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "o_status_message" => &$status_message
                        ];

                        DB::executeProcedure("TIMS.TRAINING_MANAGEMENT_PKG.SCHEDULE_SUPPORT_INFO_PR", $params_dtl);
                        if ($params_dtl['o_status_code'] != 1) {
                            DB::rollBack();
                            return $params_dtl;
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    public function traineeData(Request $request)
    {
        $emp_id = $request->get("emp_id");
        $traineeData = $this->trainingManager->getTraineeInfo($emp_id);
        return $traineeData;
    }

    public function removeTraineeData(Request $request)
    {
        $assignmentId = $request->get("assignment_id");//dd('asdasd');
        $query = "SELECT COUNT(*)  AS CNT
  FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TAS, TIMS.TRAINEE_ATTENDANCE TA
 WHERE     TAS.SCHEDULE_MST_ID = TA.SCHEDULE_MST_ID
       AND TAS.ASSIGNMENT_ID = '". $assignmentId[0] ."'";
        $result = DB::selectOne(DB::raw($query));

        if(intval($result->cnt) == 0){
            try {

                /*$sql = TraineeAssignmentSchedule::where('assignment_id', $assignmentId)->first();
                TraineeEvaluationMaster::where([
                    ['schedule_mst_id', '=', $sql->schedule_mst_id],
                    ['trainee_id', '=', $sql->trainee_id]
                ])->delete();

                $query = "DELETE FROM TRAINEE_ASSIGNMENT_SCHEDULE WHERE ASSIGNMENT_ID = '" . $assignmentId . "'";
                $result = DB::select(DB::raw($query));

                DB::commit();*/
                $sql = TraineeAssignmentSchedule::whereIn('assignment_id', $assignmentId)->
                select('schedule_mst_id','trainee_id')->get();
                //dd($sql[0]['schedule_mst_id']);

                foreach ($request->get('assignment_id') as $indx => $value) {
                    TraineeEvaluationMaster::where([
                        ['schedule_mst_id', '=', $sql[$indx]['schedule_mst_id']],
                        ['trainee_id', '=', $sql[$indx]['trainee_id']]
                    ])->delete();//dd($res);
                }
                foreach ($request->get('assignment_id') as $indx => $value) {
                    TraineeAssignmentSchedule::where('assignment_id', $request->get("assignment_id")[$indx])->delete();
                }
                return '1';
                //return $result;
            } catch (\Exception $e) {
                DB::rollBack();
                //return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
                return '0';
            }
            //return '1';
        }
        else{
            return '0';
        }
    }

    public function removeTrainerData(Request $request)
    {
        /* cpa cr according to Sumaiya 08/05/23*/
//        $query = "SELECT COUNT (*) AS CNT
//  FROM TIMS.TRAINING_SCHEDULE_DTL TSD, TIMS.TRAINEE_ATTENDANCE TA
// WHERE     TSD.SCHEDULE_ID = TA.SCHEDULE_MST_ID
//       AND TSD.SCHEDULE_DTL_ID = '" . $request->get("schedule_dtl_id")[0] . "'";
//        $result = DB::selectOne(DB::raw($query));//dd($request->get("schedule_dtl_id"));
//
//        if(intval($result->cnt) == 0){
            try {
                /*$query = "DELETE FROM TRAINING_SCHEDULE_DTL WHERE SCHEDULE_DTL_ID = '" . $request->get("schedule_dtl_id") . "'";
                $result = DB::select(DB::raw($query));
                DB::commit();*/
                foreach ($request->get('schedule_dtl_id') as $indx => $value) {
                    TrainingScheduleDtl::where('schedule_dtl_id', $request->get("schedule_dtl_id")[$indx])->delete();
                }
                return '1';
                //return $result;
            } catch (\Exception $e) {
                DB::rollBack();
                //return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
                return '0';
            }
            //return '1';
//        }
//        else{
//            return '0';
//        }
    }

    public function removeExamTypeData(Request $request)
    {
        $query = "SELECT COUNT (*) AS CNT
  FROM TIMS.TRAINEE_EXAM_SCHEDULE TES, TIMS.TRAINEE_ATTENDANCE TA
 WHERE     TES.SCHEDULE_MST_ID = TA.SCHEDULE_MST_ID
       AND TES.TRAINEE_EXAM_SCH_ID = '" . $request->get("trainee_exam_sch_id")[0] . "'";
        $result = DB::selectOne(DB::raw($query));

        if(intval($result->cnt) == 0){
            try {
                /*$query = "DELETE FROM TRAINEE_EXAM_SCHEDULE WHERE TRAINEE_EXAM_SCH_ID = '" . $request->get("trainee_exam_sch_id") . "'";
                $result = DB::select(DB::raw($query));
                DB::commit();*/
                //return $result;
                foreach ($request->get('trainee_exam_sch_id') as $indx => $value) {
                    TraineeExamSchedule::where('trainee_exam_sch_id', $request->get("trainee_exam_sch_id")[$indx])->delete();
                }
                return '1';
            } catch (\Exception $e) {
                DB::rollBack();
                //return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
                return '0';
            }
        }
        else{
            return '0';
        }
    }

    public function removeSupportingStuffData(Request $request)
    {
        $query = "SELECT COUNT (*) AS CNT
  FROM TIMS.SCHEDULE_SUPPORT_INFO TES, TIMS.TRAINEE_ATTENDANCE TA
 WHERE     TES.SCHEDULE_MST_ID = TA.SCHEDULE_MST_ID
       AND TES.SUPPORT_MEMBER_ID = '" . $request->get("support_member_id")[0] . "'";
        $result = DB::selectOne(DB::raw($query));

        if(intval($result->cnt) == 0){
            try {
                foreach ($request->get('support_member_id') as $indx => $value) {
                    ScheduleSupportInfo::where('support_member_id', $request->get("support_member_id")[$indx])->delete();
                }
                return '1';
            } catch (\Exception $e) {
                DB::rollBack();
                //return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
                return '0';
            }
        }
        else{
            return '0';
        }
    }

}
