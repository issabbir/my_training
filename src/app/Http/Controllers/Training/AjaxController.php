<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LDepartment;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Training\LTraineeType;
use App\Entities\Training\LTrainer;
use App\Entities\Training\TrainingInfo;
use App\Entities\Training\BillInfo;
use App\Entities\Training\TrainingScheduleMaster;
use App\Enums\Pmis\Employee\Statuses;
use App\Enums\ScheduleStatus;
use App\Http\Controllers\Controller;
use App\Contracts\LookupContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Managers\LookupManager;
use App\Managers\Pmis\Employee\EmployeeManager;
use App\Managers\TrainingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    /** @var EmployeeManager */
    private $employeeManager;

    /** @var LookupManager */
    private $lookupManager;


    public function __construct(EmployeeContract $employeeManager,
                                LookupContract $lookupManager, TrainingManager $trainingManager)
    {
        $this->employeeManager = $employeeManager;
        $this->lookupManager = $lookupManager;
        $this->trainingManager = $trainingManager;
    }

    public function employees(Request $request)
    {
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findEmployeeCodesBy($searchTerm);

        return $employees;
    }

    public function employeesWithName(Request $request)
    {
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findEmployeesWithNameBy($searchTerm);

        return $employees;
    }

    public function employeesWithDept(Request $request, $empDept)
    {
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findDeptWiseEmployeeCodesBy($searchTerm, $empDept);

        return $employees;
    }

    public function employee(Request $request, $empId)
    {
        return $this->employeeManager->findEmployeeInformation($empId);
    }


    public function districts(Request $request, $divisionId)
    {
        $districts = [];

        if ($divisionId) {
            $districts = $this->lookupManager->findDistrictsByDivision($divisionId);
        }

        $html = view('ajax.districts')->with('districts', $districts)->render();

        return response()->json(array('html' => $html));
    }

    public function thanas(Request $request, $districtId)
    {
        $thanas = [];

        if ($districtId) {
            $thanas = $this->lookupManager->findThanasByDistrict($districtId);
        }

        $html = view('ajax.thanas')->with('thanas', $thanas)->render();

        return response()->json(array('html' => $html));
    }

    public function branches(Request $request, $bankId)
    {
        $branches = [];

        if ($bankId) {
            $branches = $this->lookupManager->findBranchesByBank($bankId);
        }

        $html = view('ajax.branches')->with('branches', $branches)->render();

        return response()->json(array('html' => $html));
    }

    public function deptName(Request $request)
    {
        $searchTerm = $request->get('term');
        $deptName = LDepartment::select('*')
            ->where(
                [
                    ['department_name', 'like', '' . $searchTerm . '%'],
                ]
            )->orderBy('department_name', 'ASC')->limit(10)->get(['department_id', 'department_name']);

        return $deptName;
    }

    public function trainingNo(Request $request)
    {
        $searchTerm = $request->get('term');
        $trainingNo = TrainingInfo::select('*')
            ->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(training_title)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('training_number', 'like', '' . trim($searchTerm) . '%');
            })
            ->orderBy('training_number', 'ASC')->limit(10)->get();

        return $trainingNo;
    }

    public function empCode(Request $request)
    {
        $searchTerm = $request->get('term');
        $trainingNo = Employee::select('emp_code')
            ->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(emp_code)'), 'like', strtolower('%' . trim($searchTerm) . '%'));
            })
            ->orderBy('emp_code', 'ASC')->limit(10)->get();

        return $trainingNo;
    }

    public function trainingDetails(Request $request, $trainingId)
    {
        return TrainingInfo::find($trainingId);
    }

    public function trainerName(Request $request)
    {
        $searchTerm = $request->get('term');
        $trainerName = LTrainer::select('*')
            ->where(
                [
                    ['trainer_name', 'like', '' . $searchTerm . '%'],
                ]
            )->orderBy('trainer_name', 'ASC')->limit(10)->get(['trainer_id', 'trainer_name']);

        return $trainerName;
    }

    public function scheduleNo(Request $request)
    {

        $searchTerm = $request->get('term');
        $scheduleNo = TrainingScheduleMaster::select('*')
            ->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(batch_id)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('batch_id', 'like', '' . trim($searchTerm) . '%');
            })->orderBy('schedule_id', 'ASC')->limit(10)->get();

        return $scheduleNo;
    }

    public function getExamType($batch_id)
    {
        $examType = [];

        if ($batch_id) {
            $examType = $this->trainingManager->getExamType($batch_id);
        }

        $html = view('ajax.examtype')->with('examType', $examType)->render();

        return response()->json(array('html' => $html));
    }

    public function getDeptWiseEmp($dept_id)
    {
        $empList = [];

        if ($dept_id) {
            $empList = Employee::select('*')
                ->where(
                    [
                        ['dpt_department_id', '=', $dept_id]
                        //['emp_status_id', '=', Statuses::ON_ROLE]
                    ]
                )->get();
        }

        $html = view('ajax.emplist')->with('emplist', $empList)->render();

        return response()->json(array('html' => $html));
    }

    public function getDeptHead($dept_id)
    {
//        $querys = "SELECT E.DPT_DEPARTMENT_ID DPT_ID, D.DEPARTMENT_NAME, E.EMP_NAME, E.EMP_ID, E.EMP_CODE
//    FROM PMIS.EMPLOYEE    E
//         JOIN CPA_SECURITY.SEC_USERS SU ON SU.EMP_ID = E.EMP_ID
//         JOIN CPA_SECURITY.SEC_USER_ROLES SUR ON SUR.USER_ID = SU.USER_ID
//         JOIN CPA_SECURITY.SEC_ROLE SR ON SR.ROLE_ID = SUR.ROLE_ID,
//         PMIS.L_DEPARTMENT D
//   WHERE E.DPT_DEPARTMENT_ID = D.DEPARTMENT_ID AND SR.ROLE_KEY = 'HOD'
//   and E.DPT_DEPARTMENT_ID = '$dept_id'
//ORDER BY 1";

        $querys = "SELECT distinct E.DPT_DEPARTMENT_ID DPT_ID, D.DEPARTMENT_NAME, E.EMP_NAME, E.EMP_ID, E.EMP_CODE
    FROM PMIS.EMPLOYEE    E
         JOIN CPA_SECURITY.SEC_USERS SU ON SU.EMP_ID = E.EMP_ID
         JOIN CPA_SECURITY.SEC_USER_ROLES SUR ON SUR.USER_ID = SU.USER_ID
         JOIN CPA_SECURITY.SEC_ROLE SR ON SR.ROLE_ID = SUR.ROLE_ID,
         PMIS.L_DEPARTMENT D
   WHERE E.CURRENT_DEPARTMENT_ID = D.DEPARTMENT_ID AND SR.ROLE_KEY = 'HOD'
  and E.CURRENT_DEPARTMENT_ID = nvl($dept_id, E.CURRENT_DEPARTMENT_ID)
ORDER BY 1";

        $deptHead = DB::select(DB::raw($querys));
        return $deptHead;
    }

    public function getTrainingWiseEmp($training_id)
    {
        $empList = [];

//        dd($training_id);

        if ($training_id) {

//            $querys = "SELECT
//    TE.TRAINEE_ID AS trainee_id,
//    'External' AS trainee_code,
//    TE.TRAINEE_NAME AS trainee_name,
//    TE.ORGANIZATION_NAME AS organization_name,
//    TE.DESIGNATION_NAME AS desig_name,
//    TE.DEPARTMENT_NAME AS dept_name
//FROM
//    TIMS.TRAINEE TE
//UNION ALL
//SELECT
//    PE.EMP_ID AS trainee_id,
//    PE.EMP_CODE AS  trainee_code,
//    PE.EMP_NAME AS trainee_name,
//    'CPA' AS organization_name,
//    PLDE.DESIGNATION AS desig_name,
//    PLD.DEPARTMENT_NAME AS dept_name
//FROM
//    PMIS.EMPLOYEE PE, PMIS.L_DEPARTMENT PLD, PMIS.L_DESIGNATION PLDE, TIMS.NOMINATED_TRAINEE_DTL NTD
//    WHERE PE.DPT_DEPARTMENT_ID = PLD.DEPARTMENT_ID
//    AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
//    --AND PE.EMP_STATUS_ID = '1'
//    AND PE.EMP_ID = NTD.TRAINEE_ID
//    AND (NTD.STATUS = '1' OR NTD.STATUS = '2')
//    AND NTD.TRAINING_ID = $training_id";


            $querys = "SELECT a.*
  FROM (SELECT DISTINCT TE.TRAINEE_ID            AS trainee_id,
                        'External'               AS trainee_code,
                        TE.TRAINEE_NAME          AS trainee_name,
                        TE.ORGANIZATION_NAME     AS organization_name,
                        TE.DESIGNATION_NAME      AS desig_name,
                        TE.DEPARTMENT_NAME       AS dept_name,
                        NULL                     TRAINING_ID
          FROM TIMS.TRAINEE TE
        UNION ALL
        SELECT DISTINCT PE.EMP_ID               AS trainee_id,
                        PE.EMP_CODE             AS trainee_code,
                        PE.EMP_NAME             AS trainee_name,
                        'CPA'                   AS organization_name,
                        PLDE.DESIGNATION        AS desig_name,
                        PLD.DEPARTMENT_NAME     AS dept_name,
                        TSM.TRAINING_ID
          FROM PMIS.EMPLOYEE                     PE,
               PMIS.L_DEPARTMENT                 PLD,
               PMIS.L_DESIGNATION                PLDE,
               TIMS.NOMINATED_TRAINEE_DTL        NTD,
               TIMS.TRAINING_SCHEDULE_MST        TSM,
               TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  tas
         WHERE     PE.DPT_DEPARTMENT_ID = PLD.DEPARTMENT_ID(+)
               AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
               --AND PE.EMP_STATUS_ID = '1'
               AND PE.EMP_ID = NTD.TRAINEE_ID(+)
               AND NTD.TRAINING_ID = TSM.TRAINING_ID(+)
               AND TSM.BATCH_ID = tas.BATCH_ID(+)
               AND (NTD.STATUS = '1' OR NTD.STATUS = '2')
               AND NTD.TRAINING_ID = $training_id) a
 WHERE a.trainee_id NOT IN
           (SELECT TAS.TRAINEE_ID
              FROM TIMS.TRAINING_SCHEDULE_MST        TSM,
                   TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  tas
             WHERE     TSM.BATCH_ID = TAS.BATCH_ID
                   AND TSM.SCHEDULE_ID = TAS.SCHEDULE_MST_ID
                   AND TAS.TRAINEE_ID = a.trainee_id
                   AND TSM.TRAINING_ID = a.TRAINING_ID)";

            $empList = DB::select(DB::raw($querys));
        }

        $html = view('ajax.traineelist')->with('emplist', $empList)->render();

        return response()->json(array('html' => $html));
    }

    public function getTrainingWiseEmpForAll($training_id)
    {
        $empList = [];

        if ($training_id) {
//            $querys = "SELECT
//    TE.TRAINEE_ID AS trainee_id,
//    'External' AS trainee_code,
//    TE.TRAINEE_NAME AS trainee_name,
//    TE.ORGANIZATION_NAME AS organization_name,
//    TE.DESIGNATION_NAME AS desig_name,
//    TE.DEPARTMENT_NAME AS dept_name
//FROM
//    TIMS.TRAINEE TE
//UNION ALL
//SELECT
//    PE.EMP_ID AS trainee_id,
//    PE.EMP_CODE AS  trainee_code,
//    PE.EMP_NAME AS trainee_name,
//    'CPA' AS organization_name,
//    PLDE.DESIGNATION AS desig_name,
//    PLD.DEPARTMENT_NAME AS dept_name
//FROM
//    PMIS.EMPLOYEE PE, PMIS.L_DEPARTMENT PLD, PMIS.L_DESIGNATION PLDE, TIMS.NOMINATED_TRAINEE_DTL NTD
//    WHERE PE.DPT_DEPARTMENT_ID = PLD.DEPARTMENT_ID
//    AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
//    --AND PE.EMP_STATUS_ID = '1'
//    AND PE.EMP_ID = NTD.TRAINEE_ID
//    AND (NTD.STATUS = '1' OR NTD.STATUS = '2')
//    AND NTD.TRAINING_ID = $training_id";

            $querys = "SELECT a.*
  FROM (SELECT DISTINCT TE.TRAINEE_ID            AS trainee_id,
                        'External'               AS trainee_code,
                        TE.TRAINEE_NAME          AS trainee_name,
                        TE.ORGANIZATION_NAME     AS organization_name,
                        TE.DESIGNATION_NAME      AS desig_name,
                        TE.DEPARTMENT_NAME       AS dept_name,
                        NULL                     TRAINING_ID
          FROM TIMS.TRAINEE TE
        UNION ALL
        SELECT DISTINCT PE.EMP_ID               AS trainee_id,
                        PE.EMP_CODE             AS trainee_code,
                        PE.EMP_NAME             AS trainee_name,
                        'CPA'                   AS organization_name,
                        PLDE.DESIGNATION        AS desig_name,
                        PLD.DEPARTMENT_NAME     AS dept_name,
                        TSM.TRAINING_ID
          FROM PMIS.EMPLOYEE                     PE,
               PMIS.L_DEPARTMENT                 PLD,
               PMIS.L_DESIGNATION                PLDE,
               TIMS.NOMINATED_TRAINEE_DTL        NTD,
               TIMS.TRAINING_SCHEDULE_MST        TSM,
               TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  tas
         WHERE     PE.DPT_DEPARTMENT_ID = PLD.DEPARTMENT_ID(+)
               AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
               --AND PE.EMP_STATUS_ID = '1'
               AND PE.EMP_ID = NTD.TRAINEE_ID(+)
               AND NTD.TRAINING_ID = TSM.TRAINING_ID(+)
               AND TSM.BATCH_ID = tas.BATCH_ID(+)
               AND (NTD.STATUS = '1' OR NTD.STATUS = '2')
               AND NTD.TRAINING_ID = $training_id) a
 WHERE a.trainee_id NOT IN
           (SELECT TAS.TRAINEE_ID
              FROM TIMS.TRAINING_SCHEDULE_MST        TSM,
                   TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  tas
             WHERE     TSM.BATCH_ID = TAS.BATCH_ID
                   AND TSM.SCHEDULE_ID = TAS.SCHEDULE_MST_ID
                   AND TAS.TRAINEE_ID = a.trainee_id
                   AND TSM.TRAINING_ID = a.TRAINING_ID)";

            $empList = DB::select(DB::raw($querys));
        }

        return $empList;
    }

    public function getAllTraineeInfo($schedule_mst_id)
    {
        $traineeInfo = [];

        if ($schedule_mst_id) {
            $traineeInfo = $this->trainingManager->getAllTraineeInfo($schedule_mst_id);
        }

        $html = view('ajax.traineeInfo')->with('traineeInfo', $traineeInfo)->render();

        return response()->json(array('html' => $html));
    }

    public function getAllBatchWiseFeedBackTrainee($schedule_mst_id)
    {
        $traineeInfo = [];

        if ($schedule_mst_id) {
            $traineeInfo = $this->trainingManager->getAllBatchWiseFeedBackTrainee($schedule_mst_id);
        }

        $html = view('ajax.traineeInfo')->with('traineeInfo', $traineeInfo)->render();

        return response()->json(array('html' => $html));
    }

    public function getTotalDays($startDate, $endDate)
    {
        $datetime1 = new \DateTime($startDate);
        $datetime2 = new \DateTime($endDate);

        $difference = $datetime1->diff($datetime2);

        return $difference->d + 1;
    }

    public function selectDate(Request $request)
    {
        $batch_id = $request->get('batch_id');
        $schedule_id = $request->get('schedule_id');

        try {
            $status = TrainingScheduleMaster::where('schedule_id', $schedule_id)->first('schedule_status_id');
            if ($status->schedule_status_id == 2) {
                $querys = "
SELECT (SELECT COUNT (*)
          FROM (SELECT DISTINCT TRAINING_DATE
                  FROM TRAINING_SCHEDULE_DTL
                 WHERE schedule_id = $schedule_id)) as
           total_training_days,
RE_SCHEDULE_START_DATE as TRAINING_START_DATE,
       RE_SCHEDULE_END_DATE as TRAINING_END_DATE,
       RE_SCHEDULE_END_DATE - RE_SCHEDULE_START_DATE + 1     count_day
  FROM TRAINING_SCHEDULE_MST
  WHERE schedule_id = $schedule_id";
            } else {
                $querys = "SELECT (SELECT COUNT (*)
          FROM (SELECT DISTINCT TRAINING_DATE
                  FROM TRAINING_SCHEDULE_DTL
                 WHERE schedule_id = $schedule_id)) as
           total_training_days,
       TRAINING_START_DATE,
       TRAINING_END_DATE,
       TRAINING_END_DATE - TRAINING_START_DATE + 1
           count_day
  FROM TRAINING_SCHEDULE_MST
 WHERE schedule_id = $schedule_id";
            }
            $result = DB::selectOne(DB::raw($querys));

            return $result->count_day.'+'.$result->total_training_days;

        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
    }

    public function trainingBatchNo(Request $request, $trainingInfoId)
    {
        $batchNo = TrainingScheduleMaster::select('*')->where('training_schedule_mst.training_id', '=', $trainingInfoId)->get();

        $batchNoHtml = view('ajax.batchno')->with('batchNo', $batchNo)->render();

        return ['batchNoHtml' => $batchNoHtml];
    }

    public function trainingExistNo(Request $request, $batchNO)
    {

        $bill = BillInfo::where('schedule_id', $batchNO)->get();

        $bill_count = count($bill);
        return response()->json([
            'bill'=>$bill,
            'bill_count'=>$bill_count,
        ], 200);
    }

    public function batchTraineeName(Request $request, $scheduleId)
    {
        $traineeInfo = $this->trainingManager->traineeassignmentscheduleData($scheduleId);
        $traineeInfoHtml = view('ajax.traineeInfo')->with('traineeInfo', $traineeInfo)->render();

        return ['traineeInfoHtml' => $traineeInfoHtml];
    }

    public function getTraineeType(Request $request, $traineeTypeId)
    {
        $traineeType = LTraineeType::where('trainee_type_id', '=', $traineeTypeId)->first();

        return $traineeType;
    }

    public function deptWiseTrainee(Request $request)
    {
        $userId = auth()->user()->emp_id;
        $userInfo = Employee::where('emp_id', '=', $userId)->first();

        $searchTerm = $request->get('term');

        $role_key = json_decode(Auth::user()->roles->pluck('role_key'));
        if (in_array("TRAINING_ENTRY", $role_key)) {//dd('TRAINING_ENTRY');
            return Employee::/*where(
                [
                    //['emp_status_id', '=', Statuses::ON_ROLE],
                    ['current_department_id', '=', $userInfo->current_department_id],
                ]
            )->*/ whereIn(
                'emp_status_id', [1, 13])->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
            }
            )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name', 'current_department_id']);
        } else {//dd('OTHERS');
            return Employee::where(
                [
                    //['emp_status_id', '=', Statuses::ON_ROLE],
                    ['current_department_id', '=', $userInfo->current_department_id],
                ]
            )->whereIn(
                'emp_status_id', [1, 13])->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
            }
            )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name', 'current_department_id']);
        }
//        return Employee::where(
//            [
//                ['emp_code', 'like', ''.$searchTerm.'%'],
//                ['emp_status_id', '=', Statuses::ON_ROLE],
//                ['dpt_department_id', '=', $userInfo->dpt_department_id],
//            ]
////        )->orWhere(
////            [
////                ['emp_name', 'like', '%'.$searchTerm.'%'],
////                ['emp_status_id', '=', Statuses::ON_ROLE],
////                ['dpt_department_id', '=', $userInfo->dpt_department_id],
////            ]
//        )->orWhere(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%')
//        )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name', 'dpt_department_id']);

        //'dpt_department_id' changed to 'current_department_id' according to 7-Feb-22 CR by Rabbani

    }

    public function deptWiseTraineeDtl(Request $request, $id)
    {
        $query = <<<QUERY
select LD.DEPARTMENT_ID, LD.DEPARTMENT_NAME, addt.DEPARTMENT_CAPACITY from PMIS.L_DEPARTMENT LD
 left join tims.assign_department_dtl addt on addt.ASSIGN_DEPT_ID = LD.DEPARTMENT_ID
 where addt.ASSIGNMENT_MST_ID = :mst_id
QUERY;

        $department = DB::select($query, ['mst_id' => $id]);

        $items = array();
        foreach ($department as $indx => $value) {
            $items[] = $department[$indx]->department_id;
        }

        //dd($items);
        $userId = auth()->user()->emp_id;
        $userInfo = Employee::where('emp_id', '=', $userId)->first();

        $searchTerm = $request->get('term');

        $role_key = json_decode(Auth::user()->roles->pluck('role_key'));
        if (in_array("TRAINING_ENTRY", $role_key)) {//dd('TRAINING_ENTRY');
            return Employee::/*where(
                [
                    //['emp_status_id', '=', Statuses::ON_ROLE],
                    ['current_department_id', '=', $userInfo->current_department_id],
                ]
            )->*/ whereIn(
                'emp_status_id', [1, 13])->whereIn(
                'dpt_department_id', $items)->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
            }
            )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name', 'current_department_id']);
        } else {//dd('OTHERS');
            return Employee::where(
                [
                    //['emp_status_id', '=', Statuses::ON_ROLE],
                    ['current_department_id', '=', $userInfo->current_department_id],
                ]
            )->whereIn(
                'emp_status_id', [1, 13])->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
            }
            )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name', 'current_department_id']);
        }
//        return Employee::where(
//            [
//                ['emp_code', 'like', ''.$searchTerm.'%'],
//                ['emp_status_id', '=', Statuses::ON_ROLE],
//                ['dpt_department_id', '=', $userInfo->dpt_department_id],
//            ]
////        )->orWhere(
////            [
////                ['emp_name', 'like', '%'.$searchTerm.'%'],
////                ['emp_status_id', '=', Statuses::ON_ROLE],
////                ['dpt_department_id', '=', $userInfo->dpt_department_id],
////            ]
//        )->orWhere(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%')
//        )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name', 'dpt_department_id']);

        //'dpt_department_id' changed to 'current_department_id' according to 7-Feb-22 CR by Rabbani

    }

    public function allEmployee(Request $request)
    {
        $searchTerm = $request->get('term');
        return Employee::where(
            [
                ['emp_code', 'like', '' . $searchTerm . '%'],
                ['emp_status_id', '=', Statuses::ON_ROLE],
            ]
        )->orWhere(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%')
        )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name', 'dpt_department_id']);
    }


}
