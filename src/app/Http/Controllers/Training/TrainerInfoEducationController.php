<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LExam;
use App\Entities\Admin\LExamBody;
use App\Entities\Pmis\Employee\EmpEducation;
use App\Entities\Training\LTrainer;
use App\Entities\Training\LTrainerEducation;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerInfoEducationController extends Controller
{
    use HasPermission;

    public function post($trainerId, Request $request)
    {
        $response = $this->trainer_education_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('trainer-information.trainer-education-get', $trainerId);
    }

    public function form($id, Request $request)
    {
        $trainerId = $id;
        $educationList = LTrainerEducation::where('trainer_id', $trainerId)->get();

        $educationId = $request->get('education_id');
        $educationData = '';
        if ($educationId) {
            $educationData = LTrainerEducation::where('education_id', $educationId)->where('trainer_id', $trainerId)->first();
        }

        $trainerDetails = LTrainer::find($trainerId);
        $empEducationList = [];
        if ($trainerDetails) {
            $emp_id = $trainerDetails->emp_id;
            $empEducationList = EmpEducation::select('*')
                ->leftJoin('pmis.l_exam', 'pmis.l_exam.exam_id', '=', 'pmis.emp_education.exam_id')
                ->leftJoin('pmis.l_exam_body', 'pmis.l_exam_body.exam_body_id', '=', 'pmis.emp_education.exam_body_id')
                ->leftJoin('pmis.l_exam_result', 'pmis.l_exam_result.exam_result_id', '=', 'pmis.emp_education.exam_result_id')
                ->where('pmis.emp_education.emp_id', $emp_id)
                ->get();
        }

        return view('training.trainerinfo.trainer_education',
            [
                'trainer_id' => $id,
                'examBody' => LExamBody::all(),
                'exams' => LExam::all(),
                'educationList' => $educationList,
                'educationView' => $educationData,
                'empEducationList' => $empEducationList,
                'trainerDetails' => $trainerDetails
            ]);
    }

    public function update($trainerId, $teid, Request $request)
    {
        $response = $this->trainer_education_api_upd($request, $teid);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('trainer-information.trainer-education-get', [$trainerId, 'education_id' => $teid]);
    }

    private function trainer_education_api_ins(Request $request)
    {
        $postData = $request->post();

        $trainerCertificateFileName = '';
        $trainerCertificateFileType = '';
        $trainerCertificateFileContent = '';
        $trainerCertificateFile = $request->file('certificate');

        if ($trainerCertificateFile) {
            $trainerCertificateFileName = $trainerCertificateFile->getClientOriginalName();
            $trainerCertificateFileType = $trainerCertificateFile->getMimeType();
            $trainerCertificateFileContent = base64_encode(file_get_contents($trainerCertificateFile->getRealPath()));
        }

        $trainerTranscriptFileName = '';
        $trainerTranscriptFileType = '';
        $trainerTranscriptFileContent = '';
        $trainerTranscriptFile = $request->file('transcript');

        if ($trainerTranscriptFile) {
            $trainerTranscriptFileName = $trainerTranscriptFile->getClientOriginalName();
            $trainerTranscriptFileType = $trainerTranscriptFile->getMimeType();
            $trainerTranscriptFileContent = base64_encode(file_get_contents($trainerTranscriptFile->getRealPath()));
        }

        try {
            $education_id = null;
            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_EDUCATION_ID' => $education_id,
                'P_TRAINER_ID' => $postData['trainer_id'],

                'P_EXAM_ID' => $postData['trainer_exam'],
                'P_SUBJECT' => $postData['subject'],
                'P_SUBJECT_BN' => $postData['subject_bn'],
                'P_EXAM_BODY_ID' => $postData['trainer_exam_body'],
                'P_PASS_YEAR' => $postData['pass_year'],
                'P_EXAM_RESULT_ID' => null,
                'P_CERTIFFICTE_PHOTO' => [
                    'value' => $trainerCertificateFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_CERTIFICATE_TYPE' => $trainerCertificateFileType,
                'P_CERTIFICATE_NAME' => $trainerCertificateFileName,
                'P_TRANSCRIPT_PHOTO' => [
                    'value' => $trainerTranscriptFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_TRANSCRIPT_TYPE' => $trainerTranscriptFileType,
                'P_TRANSCRIPT_NAME' => $trainerTranscriptFileName,
                'P_EXAM_NAME' => null,
                'P_EXAM_NAME_BN' => null,
                'P_EXAM_BODY_NAME' => null,
                'P_EXAM_BODY_NAME_BN' => null,
                'P_EXAM_RESULT' => $postData['exam_result'],
                'P_EXAM_RESULT_BN' => null,
                'P_OTHER_EXAM_BODY_YN' => ($postData['other_exam_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_EXAM_BODY_FOREIGN_YN' => ($postData['foreign_exam_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TIMS_TRAINER_EDUCATION_ENTRY', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function trainer_education_api_upd(Request $request, $educationId)
    {
        $postData = $request->post();
        $trainerCertificateFile = $request->file('certificate');

        if ($trainerCertificateFile) {
            $trainerCertificateFileName = $trainerCertificateFile->getClientOriginalName();
            $trainerCertificateFileType = $trainerCertificateFile->getMimeType();
            $trainerCertificateFileContent = base64_encode(file_get_contents($trainerCertificateFile->getRealPath()));
        } else {
            $educationInfo = LTrainerEducation::find($educationId);
            $trainerCertificateFileName = $educationInfo->certificate_name;
            $trainerCertificateFileType = $educationInfo->certificate_type;
            $trainerCertificateFileContent = $educationInfo->certifficte_photo;
        }

        $trainerTranscriptFile = $request->file('transcript');

        if ($trainerTranscriptFile) {
            $trainerTranscriptFileName = $trainerTranscriptFile->getClientOriginalName();
            $trainerTranscriptFileType = $trainerTranscriptFile->getMimeType();
            $trainerTranscriptFileContent = base64_encode(file_get_contents($trainerTranscriptFile->getRealPath()));
        } else {
            $educationInfo = LTrainerEducation::find($educationId);
            $trainerTranscriptFileName = $educationInfo->transcript_name;
            $trainerTranscriptFileType = $educationInfo->transcript_type;
            $trainerTranscriptFileContent = $educationInfo->transcript_photo;
        }

        try {

            $o_trainer_id = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_EDUCATION_ID' => $educationId,
                'P_TRAINER_ID' => $postData['trainer_id'],
                'P_EXAM_ID' => $postData['trainer_exam'],
                'P_SUBJECT' => $postData['subject'],
                'P_SUBJECT_BN' => $postData['subject_bn'],
                'P_EXAM_BODY_ID' => $postData['trainer_exam_body'],
                'P_PASS_YEAR' => $postData['pass_year'],
                'P_EXAM_RESULT_ID' => null,
                'P_CERTIFFICTE_PHOTO' => [
                    'value' => $trainerCertificateFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_CERTIFICATE_TYPE' => $trainerCertificateFileType,
                'P_CERTIFICATE_NAME' => $trainerCertificateFileName,
                'P_TRANSCRIPT_PHOTO' => [
                    'value' => $trainerTranscriptFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_TRANSCRIPT_TYPE' => $trainerTranscriptFileType,
                'P_TRANSCRIPT_NAME' => $trainerTranscriptFileName,
                'P_EXAM_NAME' => null,
                'P_EXAM_NAME_BN' => null,
                'P_EXAM_BODY_NAME' => null,
                'P_EXAM_BODY_NAME_BN' => null,
                'P_EXAM_RESULT' => $postData['exam_result'],
                'P_EXAM_RESULT_BN' => null,
                'P_OTHER_EXAM_BODY_YN' => ($postData['other_exam_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_EXAM_BODY_FOREIGN_YN' => ($postData['foreign_exam_yn'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_INSERT_BY' => auth()->id(),
                'o_trainer_id' => &$o_trainer_id,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('TIMS.TRAINING_TRAINER_INFO_PKG.TIMS_TRAINER_EDUCATION_UPD', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 999, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }
}
