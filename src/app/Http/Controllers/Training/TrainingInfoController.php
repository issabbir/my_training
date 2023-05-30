<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LDepartment;
use App\Entities\Training\LPreRequsit;
use App\Entities\Training\LTraineeType;
use App\Entities\Training\LTrainingMedia;
use App\Entities\Training\LTrainingType;
use App\Entities\Training\TrainingInfo;
use App\Entities\Training\TrainingPreRequsit;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingInfoController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        return view('training.traininginfo.training_info', [
            'trainingInfo' => null,
            'trainingType' => LTrainingType::where('STATUS_YN', 'Y')->get(),
            'traineeType' => LTraineeType::where('active_yn', 'Y')->get(),
            'traineeMedia' => LTrainingMedia::all(),
            'department' => LDepartment::all(),
            'lPreRequsit' => LPreRequsit::all()
        ]);
    }

    public function edit(Request $request, $id)
    {
        $trainingInfoData = TrainingInfo::find($id);
//        dd($trainingInfoData);
        $whom_ids = explode(", ", $trainingInfoData->trainee_type_id);
//        dd($whom_ids);

        $trainingprereq = TrainingPreRequsit::where('training_id', '=', $id)->get();

        return view('training.traininginfo.training_info', [
            'trainingInfo' => $trainingInfoData,
            'trainingprereq' => $trainingprereq,
            'trainingType' => LTrainingType::all(),
            'traineeType' => LTraineeType::where('active_yn', 'Y')->get(),
            'traineeMedia' => LTrainingMedia::all(),
            'department' => LDepartment::all(),
            'lPreRequsit' => LPreRequsit::all(),
            'whom_ids' => $whom_ids
        ]);
    }

    public function dataTableList()
    {
        $queryResult = TrainingInfo::orderBy('insert_date', 'desc')->get();
        return datatables()->of($queryResult)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('training-information.training-information-edit', [$query->training_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addColumn('course_fee', function ($query) {
                if(empty($query->course_fee)){
                    return 'N/A';
                }else{
                    return ($query->course_fee);
                }
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
        $response = $this->training_information_api_ins($request);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

//        return redirect()->route('training-information.training-information-index');
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $response = $this->training_information_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

//        return redirect()->route('training-information.training-information-index');
        return redirect()->back();
    }

    private function training_information_api_ins(Request $request)
    {
//        dd($request);

        $postData = $request->post();


        if($postData['external_yn'] === 'N') {
            $fee = '';
        }else {
            $fee = $postData['course_fee'];
        }

//        dd($postData['trainee_type']);

        $trainee_type = implode(', ', $postData['trainee_type']);

//        dd($trainee_type);
//        dd($postData['external_yn']);

        $remuneration_amount = isset($postData['remuneration_amount']) ? ($postData['remuneration_amount']) : '';
        $dept_name = isset($postData['dept_name']) ? ($postData['dept_name']) : '';
        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';

        $trainingInfoFileName = '';
        $trainingInfoFileType = '';
        $trainingInfoFileContent = '';
        $trainingInfoFile = $request->file('attachment');

        if ($trainingInfoFile) {
            $trainingInfoFileName = $trainingInfoFile->getClientOriginalName();
            $trainingInfoFileType = $trainingInfoFile->getMimeType();
            $trainingInfoFileContent = base64_encode(file_get_contents($trainingInfoFile->getRealPath()));
        }

        try {
            DB::beginTransaction();

            $training_information_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINING_ID' => [
                    'value' => &$training_information_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_TRAINING_TITLE' => $postData['training_title'],
                'P_TITLE_BN' => $postData['training_title_bn'],
                //'P_TRAINING_NO' => $postData['training_no'],
                'P_T_TYPE_ID' => $postData['training_type'],
//                'P_TRAINEE_T_ID' => $postData['trainee_type'],
                'P_TRAINEE_T_ID' => $trainee_type,
                'P_T_MEDIA_ID' => $postData['training_media_type'],
                'P_ATTACHMENT' => [
                    'value' => $trainingInfoFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_ATTACHMENT_NAME' => $trainingInfoFileName,
                'P_ATTCHMENT_TYPE' => $trainingInfoFileType,
                'P_COURSE' => $postData['no_of_course'],
                'P_OBJECTIVES' => $postData['objectives'],
//                'P_COURSE_FEE' => $postData['course_fee'],
                'P_COURSE_FEE' => $fee,
                'P_COURSE_CONTENT' => $postData['course_content'],
                'P_DEPARTMENT_YN' => '',//($postData['dept_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_DEPT_ID' => '',//$dept_name,
                'P_TRAINING_FACALITIES' => $postData['training_facilities'],
                'P_COORDINATION_EMP_ID' => $postData['emp_id'],
                'P_COORDINATION_NAME' => $postData['emp_name'],
                'P_COORDINATION_CELL' => $postData['emp_mbl'],
                'P_COORDINATION_EMAIL' => $postData['emp_email'],
                'P_DURATION' => $postData['duration'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_FORM_DATE' => $fromDate,
                'P_DATE_TO' => $toDate,
                'P_REMUNA_YN' => '',//($postData['remuneration_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMUNA_AMT' => '',//$remuneration_amount,
                'P_PARTICIPANTS' => $postData['accepted_participant'],
                'P_REMARKS' => $postData['remarks'],
                'P_EXTERNAL_YN' => $postData['external_yn'],
                'P_FILE_NO' => $postData['file_no'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

//            dd($params);

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_INFO_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            if ($request->get("chk_court")) {
                for ($i = 0, $l = count($request->get('chk_court')); $i < $l; ++$i) {
                    $pre_requsit_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_prerequsit = [
                        'P_PRE_REQUSIT_ID' => [
                            'value' => &$pre_requsit_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        'P_PRE_REQUSIT_NAME' => '',
                        'P_TRAINING_ID' => $params['P_TRAINING_ID']['value'],
                        'P_ACTIVE_YN' => 'Y',
                        'P_REQUSIT_ID' => $request->get('chk_court')[$i],
                        'P_REMARKS' => '',
                        'P_INSERT_BY' => auth()->id(),
                        'o_status_code' => &$status_code,
                        'o_status_message' => &$status_message,
                    ];
                    DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_PRE_REQUSIT_PR', $params_prerequsit);

                    if ($params_prerequsit['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_prerequsit;
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

    private function training_information_api_upd($request, $id)
    {
        $postData = $request->post();

//        dd($postData);

        if($postData['external_yn'] == 'Y') {
            $fee = $postData['course_fee'];
        }else {
            $fee = '';
        }

        $trainee_type = implode(', ', $postData['trainee_type']);

        $remuneration_amount = isset($postData['remuneration_amount']) ? ($postData['remuneration_amount']) : '';
        $dept_name = isset($postData['dept_name']) ? ($postData['dept_name']) : '';
        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';

        $trainingInfoFile = $request->file('attachment');

        if ($trainingInfoFile) {
            $trainingInfoFileName = $trainingInfoFile->getClientOriginalName();
            $trainingInfoFileType = $trainingInfoFile->getMimeType();
            $trainingInfoFileContent = base64_encode(file_get_contents($trainingInfoFile->getRealPath()));
        } else {
            $trainingInfo = TrainingInfo::find($id);
            $trainingInfoFileName = $trainingInfo->attachment_name;
            $trainingInfoFileType = $trainingInfo->attachment_type;
            $trainingInfoFileContent = $trainingInfo->attachment;
        }

        try {
            DB::beginTransaction();
            $training_information_id = $id;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_TRAINING_ID' => [
                    'value' => &$training_information_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_TRAINING_TITLE' => $postData['training_title'],
                'P_TITLE_BN' => $postData['training_title_bn'],
                //'P_TRAINING_NO' => $postData['training_no'],
                'P_T_TYPE_ID' => $postData['training_type'],
//                'P_TRAINEE_T_ID' => $postData['trainee_type'],
                'P_TRAINEE_T_ID' => $trainee_type,
                'P_T_MEDIA_ID' => $postData['training_media_type'],
                'P_ATTACHMENT' => [
                    'value' => $trainingInfoFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_ATTACHMENT_NAME' => $trainingInfoFileName,
                'P_ATTCHMENT_TYPE' => $trainingInfoFileType,
                'P_COURSE' => $postData['no_of_course'],
                'P_OBJECTIVES' => $postData['objectives'],
//                'P_COURSE_FEE' => $postData['course_fee'],
                'P_COURSE_FEE' => $fee,
                'P_COURSE_CONTENT' => $postData['course_content'],
                'P_DEPARTMENT_YN' => '',//($postData['dept_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_DEPT_ID' => '',//$dept_name,
                'P_TRAINING_FACALITIES' => $postData['training_facilities'],
                'P_COORDINATION_EMP_ID' => $postData['emp_id'],
                'P_COORDINATION_NAME' => $postData['emp_name'],
                'P_COORDINATION_CELL' => $postData['emp_mbl'],
                'P_COORDINATION_EMAIL' => $postData['emp_email'],
                'P_DURATION' => $postData['duration'],
                'P_ACTIVE_YN' => ($postData['active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_FORM_DATE' => $fromDate,
                'P_DATE_TO' => $toDate,
                'P_REMUNA_YN' => '',//($postData['remuneration_active_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_REMUNA_AMT' => '',//$remuneration_amount,
                'P_PARTICIPANTS' => $postData['accepted_participant'],
                'P_REMARKS' => $postData['remarks'],
                'P_EXTERNAL_YN' => $postData['external_yn'],
                'P_FILE_NO' => $postData['file_no'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

//            dd($params);

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_INFO_PR', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            TrainingPreRequsit::where('training_id', $training_information_id)->delete();

            if ($request->get("chk_court")) {
                for ($i = 0, $l = count($request->get('chk_court')); $i < $l; ++$i) {
                    $pre_requsit_id = null;
                    $status_code = sprintf("%4000s", "");
                    $status_message = sprintf("%4000s", "");
                    $params_prerequsit = [
                        'P_PRE_REQUSIT_ID' => [
                            'value' => &$pre_requsit_id,
                            'type' => \PDO::PARAM_INPUT_OUTPUT,
                            'length' => 255
                        ],
                        'P_PRE_REQUSIT_NAME' => '',
                        'P_TRAINING_ID' => $params['P_TRAINING_ID']['value'],
                        'P_ACTIVE_YN' => 'Y',
                        'P_REQUSIT_ID' => $request->get('chk_court')[$i],
                        'P_REMARKS' => '',
                        'P_INSERT_BY' => auth()->id(),
                        'o_status_code' => &$status_code,
                        'o_status_message' => &$status_message,
                    ];
                    DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_PRE_REQUSIT_PR', $params_prerequsit);

                    if ($params_prerequsit['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params_prerequsit;
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
}
