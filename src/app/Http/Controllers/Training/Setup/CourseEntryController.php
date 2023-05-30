<?php

namespace App\Http\Controllers\Training\Setup;

use App\Entities\Training\LCourse;
use App\Entities\Training\TrainingType;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseEntryController extends Controller
{
    use HasPermission;


    public function index(Request $request)
    {
        return view('training.setup.courseentry.courseentry', [
            'courseentry' => null
        ]);
    }

    public function edit(Request $request, $id)
    {
        $courseentry = LCourse::select('*')
            ->where('course_id', '=', $id)
            ->first();

        return view('training.setup.courseentry.courseentry', [
            'courseentry' => $courseentry
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LCourse::all();
        return datatables()->of($queryResult)
            ->addColumn('active_yn', function($query) {
                if($query->active_yn == YesNoFlag::YES){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('action', function($query) {
                return '<a href="'. route('course-entry.course-entry-edit', [$query->course_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {

        $response = $this->course_entry_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('course-entry.course-entry-index');
    }

    public function update(Request $request, $id) {
        $response = $this->course_entry_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('course-entry.course-entry-index');
    }

    private function course_entry_api_ins(Request $request)
    {
        $postData = $request->post();

        $trainingInfoFileName = '';
        $trainingInfoFileType = '';
        $trainingInfoFileContent = '';
        $trainingInfoFile = $request->file('attachment');

        if($trainingInfoFile) {
            $trainingInfoFileName = $trainingInfoFile->getClientOriginalName();
            $trainingInfoFileType = $trainingInfoFile->getMimeType();
            $trainingInfoFileContent = base64_encode(file_get_contents($trainingInfoFile->getRealPath()));
        }

        try {
            $course_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_COURSE_ID' => [
                    'value' => &$course_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_COURSE_NAME' => $postData['course_name'],
                'P_COURSE_NAME_BN' => $postData['course_name_bn'],
                'P_ACTIVE_YN' => ($postData['active_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_DESCRIPTION' => $postData['description'],
                'P_COURSE_FILE' => [
                    'value' => $trainingInfoFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_COURSE_FILE_NAME' => $trainingInfoFileName,
                'P_COURSE_FILE_TYPE' => $trainingInfoFileType,
                'P_COURSE_DURATION' => $postData['course_duration'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_COURSE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function course_entry_api_upd($request, $id)
    {

        $postData = $request->post();

        $trainingInfoFileName = '';
        $trainingInfoFileType = '';
        $trainingInfoFileContent = '';
        $trainingInfoFile = $request->file('attachment');

        if($trainingInfoFile) {
            $trainingInfoFileName = $trainingInfoFile->getClientOriginalName();
            $trainingInfoFileType = $trainingInfoFile->getMimeType();
            $trainingInfoFileContent = base64_encode(file_get_contents($trainingInfoFile->getRealPath()));
        }

        try {
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_COURSE_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_COURSE_NAME' => $postData['course_name'],
                'P_COURSE_NAME_BN' => $postData['course_name_bn'],
                'P_ACTIVE_YN' => ($postData['active_status'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO,
                'P_DESCRIPTION' => $postData['description'],
                'P_COURSE_FILE' => [
                    'value' => $trainingInfoFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_COURSE_FILE_NAME' => $trainingInfoFileName,
                'P_COURSE_FILE_TYPE' => $trainingInfoFileType,
                'P_COURSE_DURATION' => $postData['course_duration'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_LOOKUPS_PKG.TRAINING_COURSE_PR', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

}
