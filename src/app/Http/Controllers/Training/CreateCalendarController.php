<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LDepartment;
use App\Entities\Training\LPreRequsit;
use App\Entities\Training\LTraineeType;
use App\Entities\Training\LTrainingMedia;
use App\Entities\Training\LTrainingType;
use App\Entities\Training\TrainingCalenderDtl;
use App\Entities\Training\TrainingCalenderMaster;
use App\Entities\Training\TrainingInfo;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateCalendarController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        $date1 = date('Y-m-d', strtotime(date('Y-07-01')));
        $date2 = date('Y-m-d', strtotime("$date1 +364 day"));

        //$date2 = date('Y-m-d', strtotime("+12 months $date1"));
        //$oneYearOn = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day"));

        return view('training.calendar.create_calendar.calendar', [
            'calendardata' => null,
            'traininginfo' => TrainingInfo::all(),
            'date1' => $date1,
            'date2' => $date2,
            'trainingInfo' => null,
            'trainingType' => LTrainingType::all(),
            'traineeType' => LTraineeType::where('cpa_yn', 'Y')->get(),
            'traineeMedia' => LTrainingMedia::all(),
            'department' => LDepartment::all(),
            'lPreRequsit' => LPreRequsit::all()
        ]);
    }

    public function getTrainingInfoData(Request $request)
    {
        $dtl_training_id = $request->get("dtl_training_id");
        $trainingInfo = TrainingInfo::where('training_id', '=', $dtl_training_id)->get();
        return $trainingInfo;
    }

    public function edit(Request $request, $id)
    {
        $calendardata = TrainingCalenderMaster::select('*')
            ->where('calender_id', '=', $id)
            ->first();
        $calendardtldata = TrainingCalenderDtl::where('calender_id', '=', $id)->get();

        $allTraining = TrainingCalenderDtl::where('calender_id', $id)->get(['training_id'])->pluck('training_id')->toArray();
        $allTraining_count = count($allTraining);

        return view('training.calendar.create_calendar.calendar', [
            'traininginfo' => TrainingInfo::all(),
            'calendardata' => $calendardata,
            'calendardtldata' => $calendardtldata,
            'allTraining' => json_encode($allTraining),
            'allTraining_count' => $allTraining_count,
            'trainingInfo' => null,
            'trainingType' => LTrainingType::all(),
            'traineeType' => LTraineeType::where('cpa_yn', 'Y')->get(),
            'traineeMedia' => LTrainingMedia::all(),
            'department' => LDepartment::all(),
            'lPreRequsit' => LPreRequsit::all()

        ]);
    }

    public function dataTableList()
    {
        $queryResult = TrainingCalenderMaster::orderBy('insert_date', 'desc')->get();
        return datatables()->of($queryResult)
            ->addColumn('start_date', function ($query) {
                return Carbon::parse($query->start_date)->format('Y-m-d');
            })
            ->addColumn('end_date', function ($query) {
                return Carbon::parse($query->end_date)->format('Y-m-d');
            })
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function ($query) {
                return '<a href="' . route('create-calender.create-calender-edit', [$query->calender_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
        $response = $this->calendar_mst_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('create-calender.create-calender-index');
    }

    public function update(Request $request, $id)
    {
        $response = $this->calendar_information_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('create-calender.create-calender-index');
    }

    private function calendar_mst_api_ins(Request $request)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';
        $params = [];
        try {
            DB::beginTransaction();
            $calender_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_CALENDER_ID' => [
                    'value' => &$calender_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_CALENDER_NAME' => $postData['calendar_name'],
                'P_START_DATE' => $fromDate,
                'P_END_DATE' => $toDate,
                'P_DESCRIPTION' => $postData['description'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_MANDATORY_YN' => 'N',//($postData['mandatory_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_CALENDER_PKG.TRAINING_CALENDER_MST_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            } else {

                if ($request->get('training_id')) {
                    foreach ($request->get('training_id') as $indx => $value) {
                        $calendar_dtl_id = null;
                        $trainingInfoFileContent = null;
                        $trainingInfo = TrainingInfo::where('training_id', '=', $request->get('training_id')[$indx])->get();
                        $fromDate = isset($request->get('train_from_date')[$indx]) ? date('Y-m-d', strtotime($request->get('train_from_date')[$indx])) : '';
                        $toDate = isset($request->get('train_to_date')[$indx]) ? date('Y-m-d', strtotime($request->get('train_to_date')[$indx])) : '';
                        $params_dtl = [];
                        $status_code = sprintf("%4000s", "");
                        $status_message = sprintf("%4000s", "");
                        $params_dtl = [
                            "P_CALENDER_DTL_ID" => [
                                'value' => &$calendar_dtl_id,
                                'type' => \PDO::PARAM_INPUT_OUTPUT,
                                'length' => 255
                            ],
                            "P_CALENDER_ID" => $params['P_CALENDER_ID']['value'],
                            "P_TRAINING_ID" => $request->get('training_id')[$indx],
                            "P_TRAINING_NAME" => $trainingInfo[0]->training_title,
                            "P_TRAINING_NAME_BN" => '',
                            "P_TRAINING_TYPE_ID" => $trainingInfo[0]->training_type_id,
                            "P_TRAINING_MEDIA_TYPE_ID" => $trainingInfo[0]->training_media_type_id,
                            "P_NUMBER_OF_COURSE" => $trainingInfo[0]->number_of_course,
                            "P_DURATION" => $trainingInfo[0]->duration,
                            "P_OBJECTIVES" => '',
                            "P_ACTIVE_YN" => $trainingInfo[0]->active_yn,
                            "P_PROPOSED_START_DATE" => $fromDate,
                            "P_PROPOSED_END_DATE" => $toDate,
                            "P_PROPOSED_START_TIME" => '',
                            "P_PROPOSED_END_TIME" => '',
                            'P_ATTACHMENT' => [
                                'value' => $trainingInfoFileContent,
                                'type' => SQLT_CLOB,
                            ],
                            "P_ATTACHMENT_TYPE" => '',
                            "P_ATTACHMENT_NAME" => '',
                            "P_INSERT_BY" => auth()->id(),
                            "o_status_code" => &$status_code,
                            "o_status_message" => &$status_message
                        ];

                        //dd($params_dtl);
                        DB::executeProcedure("TIMS.TRAINING_CALENDER_PKG.TRAINING_CALENDER_DTL_PR", $params_dtl);
                        if ($params_dtl['o_status_code'] != 1) {
                            DB::rollBack();
                            return $params_dtl;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        DB::commit();
        return $params;
    }

    private function calendar_information_api_upd($request, $id)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';
        $params = [];
        try {
            DB::beginTransaction();

            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_CALENDER_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_CALENDER_NAME' => $postData['calendar_name'],
                'P_START_DATE' => $fromDate,
                'P_END_DATE' => $toDate,
                'P_DESCRIPTION' => $postData['description'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_MANDATORY_YN' => 'N',
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_CALENDER_PKG.TRAINING_CALENDER_MST_PR', $params);
            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            if ($request->get('training_id')) {
                if ($request->get('training_id')) {
                    $calender_id = $id;
                    TrainingCalenderDtl::where('calender_id', $calender_id)->delete();
                }

                foreach ($request->get('training_id') as $indx => $value) {
                    $trainingInfoFileContent = null;
                    $trainingInfo = TrainingInfo::where('training_id', '=', $request->get('training_id')[$indx])->get();
                    $fromDate = isset($request->get('train_from_date')[$indx]) ? date('Y-m-d', strtotime($request->get('train_from_date')[$indx])) : '';
                    $toDate = isset($request->get('train_to_date')[$indx]) ? date('Y-m-d', strtotime($request->get('train_to_date')[$indx])) : '';
                    $params_dtl = [];
                    $calender_dtl_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_dtl = [
                        "P_CALENDER_DTL_ID" => [
                            'value' => &$calender_dtl_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        "P_CALENDER_ID" => $id,
                        "P_TRAINING_ID" => $request->get('training_id')[$indx],
                        "P_TRAINING_NAME" => $trainingInfo[0]->training_title,
                        "P_TRAINING_NAME_BN" => '',
                        "P_TRAINING_TYPE_ID" => $trainingInfo[0]->training_type_id,
                        "P_TRAINING_MEDIA_TYPE_ID" => $trainingInfo[0]->training_media_type_id,
                        "P_NUMBER_OF_COURSE" => $trainingInfo[0]->number_of_course,
                        "P_DURATION" => $trainingInfo[0]->duration,
                        "P_OBJECTIVES" => '',
                        "P_ACTIVE_YN" => $trainingInfo[0]->active_yn,
                        "P_PROPOSED_START_DATE" => $fromDate,
                        "P_PROPOSED_END_DATE" => $toDate,
                        "P_PROPOSED_START_TIME" => '',
                        "P_PROPOSED_END_TIME" => '',
                        'P_ATTACHMENT' => [
                            'value' => $trainingInfoFileContent,
                            'type' => SQLT_CLOB,
                        ],
                        "P_ATTACHMENT_TYPE" => '',
                        "P_ATTACHMENT_NAME" => '',
                        "P_INSERT_BY" => auth()->id(),
                        "o_status_code" => &$status_code,
                        "o_status_message" => &$status_message
                    ];
                    DB::executeProcedure("TIMS.TRAINING_CALENDER_PKG.TRAINING_CALENDER_DTL_PR", $params_dtl);
                    if ($params_dtl['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_dtl;
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        DB::commit();
        return $params;
    }

    public function removeDetailData(Request $request)
    {
        try {
            $querys = "DELETE FROM TRAINING_CALENDER_DTL WHERE CALENDER_DTL_ID = '" . $request->get("calender_dtl_id") . "'";
            $result = DB::select(DB::raw($querys));
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
    }

}
