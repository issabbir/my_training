<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/28/20
 * Time: 3:45 PM
 */

namespace App\Managers;


use App\Contracts\TrainingContract;
use App\Entities\Training\TrainingScheduleMaster;
use Illuminate\Support\Facades\DB;

class TrainingManager implements TrainingContract
{
    public function traineeList()
    {
        $querys = "SELECT
    TE.TRAINEE_ID AS trainee_id,
    'External' AS trainee_code,
    TE.TRAINEE_NAME AS trainee_name,
    TE.ORGANIZATION_NAME AS organization_name,
    TE.DESIGNATION_NAME AS desig_name,
    TE.DEPARTMENT_NAME AS dept_name
FROM
    TIMS.TRAINEE TE
UNION
SELECT
    PE.EMP_ID AS trainee_id,
    PE.EMP_CODE AS  trainee_code,
    PE.EMP_NAME AS trainee_name,
    'CPA' AS organization_name,
    PLDE.DESIGNATION AS desig_name,
    PLD.DEPARTMENT_NAME AS dept_name
FROM
    PMIS.EMPLOYEE PE, PMIS.L_DEPARTMENT PLD, PMIS.L_DESIGNATION PLDE
    WHERE PE.DPT_DEPARTMENT_ID = PLD.DEPARTMENT_ID
    AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
    AND PE.EMP_STATUS_ID = '1'";
    /*AND PE.EMP_TYPE_ID = 1*/
        $traineeList = DB::select(DB::raw($querys));
        //DB::commit();
        return $traineeList;
    }

    public function getTraineeInfo($emp_id)
    {
        $querys = "SELECT
    TE.TRAINEE_ID AS trainee_id,
    TE.TRAINEE_NAME AS trainee_name,
    TE.ORGANIZATION_NAME AS organization_name,
    TE.DESIGNATION_NAME AS desig_name,
    TE.DEPARTMENT_NAME AS dept_name
FROM
    TIMS.TRAINEE TE
    WHERE  TE.TRAINEE_ID = $emp_id
UNION
SELECT
    --to_char (PE.EMP_CODE) ,
    PE.EMP_ID AS trainee_id,
    PE.EMP_NAME AS trainee_name,
    'CPA' AS organization_name,
    PLDE.DESIGNATION AS desig_name,
    PLD.DEPARTMENT_NAME AS dept_name
FROM
    PMIS.EMPLOYEE PE, PMIS.L_DEPARTMENT PLD, PMIS.L_DESIGNATION PLDE
    WHERE PE.DPT_DEPARTMENT_ID = PLD.DEPARTMENT_ID
    AND PE.DESIGNATION_ID = PLDE.DESIGNATION_ID
    --AND PE.EMP_STATUS_ID = '1'
    AND PE.EMP_ID = $emp_id";

        $traineeInfo = DB::select(DB::raw($querys));
        return $traineeInfo;
    }

    public function traineeassignmentscheduleData($schedule_mst_id)
    {
        $querys = "SELECT TTAS.ASSIGNMENT_ID       AS assignment_id,
       TTAS.SCHEDULE_MST_ID     AS schedule_mst_id,
       TTAS.BATCH_ID            AS batch_id,
       TT.TRAINEE_ID            AS trainee_id,
       TT.TRAINEE_NAME          AS trainee_name,
       TT.ORGANIZATION_NAME     AS organization_name,
       'External'               AS trainee_code
  FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS, TIMS.TRAINEE TT
 WHERE     TT.TRAINEE_ID = TTAS.TRAINEE_ID
       AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id
UNION
SELECT TTAS.ASSIGNMENT_ID       AS assignment_id,
       TTAS.SCHEDULE_MST_ID     AS schedule_mst_id,
       TTAS.BATCH_ID            AS batch_id,
       PE.EMP_ID                AS trainee_id,
       PE.EMP_NAME              AS trainee_name,
       'CPA'                    AS organization_name,
       PE.EMP_CODE              AS trainee_code
  FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS, PMIS.EMPLOYEE PE
 WHERE     PE.EMP_ID = TTAS.TRAINEE_ID
       AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id";

        $assignmentSchedule = DB::select(DB::raw($querys));
        return $assignmentSchedule;
    }

    public function attendanceEntrySearch($batch_id)
    {
        $querys = "SELECT DISTINCT TTCM.SCHEDULE_ID         AS SCHEDULE_ID,
       TTCM.BATCH_ID            AS BATCH_ID,
       TT.TRAINEE_ID            AS TRAINEE_ID,
       TT.TRAINEE_NAME          AS STUDENT,
       TT.ORGANIZATION_NAME     AS ORGANIZATION_NAME
  FROM TIMS.TRAINING_SCHEDULE_MST        TTCM,
       TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
       TIMS.TRAINEE                      TT,
       TIMS.TRAINING_SCHEDULE_DTL        TSD
 WHERE     TTCM.SCHEDULE_ID = TTAS.SCHEDULE_MST_ID
       AND TT.TRAINEE_ID = TTAS.TRAINEE_ID
       AND TTCM.SCHEDULE_ID = TSD.SCHEDULE_ID
       AND TTAS.SCHEDULE_MST_ID = TSD.SCHEDULE_ID
       AND TTAS.BATCH_ID = '" . $batch_id . "'
UNION ALL
SELECT DISTINCT TTCM.SCHEDULE_ID     AS SCHEDULE_ID,
       TTCM.BATCH_ID        AS BATCH_ID,
       PE.EMP_ID            AS TRAINEE_ID,
       PE.EMP_NAME          AS STUDENT,
       'CPA'                AS ORGANIZATION_NAME
  FROM TIMS.TRAINING_SCHEDULE_MST        TTCM,
       TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
       PMIS.EMPLOYEE                     PE,
       TIMS.TRAINING_SCHEDULE_DTL        TSD
 WHERE     TTCM.SCHEDULE_ID = TTAS.SCHEDULE_MST_ID
       AND PE.EMP_ID = TTAS.TRAINEE_ID
       AND TTCM.SCHEDULE_ID = TSD.SCHEDULE_ID
       AND TTAS.SCHEDULE_MST_ID = TSD.SCHEDULE_ID
       AND TTAS.BATCH_ID = '" . $batch_id . "'";

        $attendanceEntry = DB::select(DB::raw($querys));
        return $attendanceEntry;
    }

    public function totalDays($schedule_id)
    {
        $status = TrainingScheduleMaster::where('schedule_id', $schedule_id)->first('schedule_status_id');
        if($status->schedule_status_id == 2)
        {
            $querys = "select re_schedule_start_date as training_start_date, re_schedule_end_date as training_end_date from training_schedule_mst where schedule_id = $schedule_id";
            $totalDays = DB::selectOne(DB::raw($querys));
        }else{
            $querys = "select training_start_date, training_end_date from training_schedule_mst where schedule_id = $schedule_id";
            $totalDays = DB::selectOne(DB::raw($querys));
        }

        return $totalDays;
    }

    public function getTotalTrainee($batch_id){
        $querys = "SELECT DISTINCT trainee_id, trainee_name as student, schedule_mst_id as schedule_id, batch_id, 'CPA' as organization_name
  FROM trainee_attendance
 WHERE batch_id = '$batch_id'";
        $total_trainee = DB::select(DB::raw($querys));
        return $total_trainee;
    }

    public function attendanceUpdateSearch($batch_id)
    {
        $querys = "SELECT TTA.ATTENDANCE_ID                      AS attendance_id,
       TTA.SCHEDULE_MST_ID                    AS schedule_mst_id,
       TTA.BATCH_ID                           AS batch_id,
       to_char(TTA.ATTENDANCE_DATE, 'DD-MON-YY')                   AS attendance_date,
       TT.TRAINEE_ID                         AS trainee_id,
       TT.TRAINEE_NAME                       AS student,
       TT.ORGANIZATION_NAME                   AS organization_name,
       TTA.IN_TIME                            AS in_time,
       TTA.OUT_TIME                           AS out_time,
       TTA.ATTENDANCE_YN                      AS attendance_yn,
       TO_CHAR (TTA.IN_TIME, 'hh:mi AM')      AS in_time_v,
       TO_CHAR (TTA.OUT_TIME, 'hh:mi AM')     AS out_time_v
  FROM TIMS.TRAINEE_ATTENDANCE TTA, TIMS.TRAINEE TT
 WHERE TT.TRAINEE_ID = TTA.TRAINEE_ID
    AND TTA.BATCH_ID = '" . $batch_id . "'
UNION ALL
SELECT TTA.ATTENDANCE_ID                      AS attendance_id,
       TTA.SCHEDULE_MST_ID                    AS schedule_mst_id,
       TTA.BATCH_ID                           AS Batch_Id,
       to_char(TTA.ATTENDANCE_DATE, 'DD-MON-YY')                   AS attendance_date,
       PE.EMP_ID                              AS trainee_id,
       PE.EMP_NAME                            AS student,
       'CPA'                                  AS organization_name,
       TTA.IN_TIME                            AS in_time,
       TTA.OUT_TIME                           AS out_time,
       TTA.ATTENDANCE_YN                      AS attendance_yn,
       TO_CHAR (TTA.IN_TIME, 'hh:mi AM')      AS in_time_v,
       TO_CHAR (TTA.OUT_TIME, 'hh:mi AM')     AS out_time_v
  FROM TIMS.TRAINEE_ATTENDANCE TTA, PMIS.EMPLOYEE PE
 WHERE PE.EMP_ID = TTA.TRAINEE_ID
    AND TTA.BATCH_ID = '" . $batch_id . "'
    ORDER BY attendance_date";

        $attendanceUpdate = DB::select(DB::raw($querys));
        return $attendanceUpdate;
    }

    public function evaluationEntrySearch($exam_type, $batch_id)
    {
        $querys = "SELECT        DISTINCT TT.TRAINEE_ID   AS TRAINEE_ID,
        TEM.EVALUATION_ID        AS EVALUATION_ID,
       TT.TRAINEE_NAME          AS TRAINEE_NAME,
       TT.ORGANIZATION_NAME     AS ORGANIZATION_NAME,
       TLET.EXAM_TYPE_ID        AS EXAM_TYPE_ID,
       TLET.EXAM_TYPE_NAME      AS EXAM_TYPE_NAME,
       TTES.PASS_MARKS          AS PASS_MARKS,
       TTES.TOTAL_MARKS         AS TOTAL_MARKS
  FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
       TIMS.TRAINEE_ATTENDANCE           TA,
       TIMS.L_EXAM_TYPE                  TLET,
       TIMS.TRAINEE_EVALUTION_MST        TEM,
       TIMS.TRAINEE_EXAM_SCHEDULE        TTES,
       TIMS.TRAINEE                      TT
 WHERE     TT.TRAINEE_ID = TA.TRAINEE_ID
       AND TA.TRAINEE_ID = TTAS.TRAINEE_ID
       AND TA.TRAINEE_ID = TEM.TRAINEE_ID
       AND TA.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TA.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TLET.EXAM_TYPE_ID = TTES.EXAM_TYPE_ID
       AND TEM.SCHEDULE_MST_ID = TA.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TA.ATTENDANCE_YN = 'Y'
       AND TA.BATCH_ID = '" . $batch_id . "'
       AND TTES.EXAM_TYPE_ID = '" . $exam_type . "'
UNION ALL
SELECT DISTINCT PE.EMP_ID               AS TRAINEE_ID,
        TEM.EVALUATION_ID       AS EVALUATION_ID,
       PE.EMP_NAME             AS TRAINEE_NAME,
       'CPA'                   AS ORGANIZATION_NAME,
       TLET.EXAM_TYPE_ID       AS EXAM_TYPE_ID,
       TLET.EXAM_TYPE_NAME     AS EXAM_TYPE_NAME,
       TTES.PASS_MARKS         AS PASS_MARKS,
       TTES.TOTAL_MARKS        AS TOTAL_MARKS
  FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
       TIMS.TRAINEE_ATTENDANCE           TA,
       TIMS.L_EXAM_TYPE                  TLET,
       TIMS.TRAINEE_EVALUTION_MST        TEM,
       TIMS.TRAINEE_EXAM_SCHEDULE        TTES,
       PMIS.EMPLOYEE                     PE
 WHERE     PE.EMP_ID = TA.TRAINEE_ID
       AND TA.TRAINEE_ID = TTAS.TRAINEE_ID
       AND TA.TRAINEE_ID = TEM.TRAINEE_ID
       AND TA.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TA.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TLET.EXAM_TYPE_ID = TTES.EXAM_TYPE_ID
       AND TEM.SCHEDULE_MST_ID = TA.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TA.ATTENDANCE_YN = 'Y'
       AND TA.BATCH_ID = '" . $batch_id . "'
       AND TTES.EXAM_TYPE_ID = '" . $exam_type . "'";
        //dd($querys);
        $evaluationEntry = DB::select(DB::raw($querys));
        return $evaluationEntry;
    }

    public function evaluationUpdateSearch($exam_type, $batch_id)
    {
        $querys = "SELECT DISTINCT TT.TRAINEE_ID     AS TRAINEE_ID,
TEM.EVALUATION_ID          AS EVALUATION_ID,
       TT.TRAINEE_NAME            AS TRAINEE_NAME,
       TT.ORGANIZATION_NAME       AS ORGANIZATION_NAME,
       TLET.EXAM_TYPE_ID          AS EXAM_TYPE_ID,
       TLET.EXAM_TYPE_NAME        AS EXAM_TYPE_NAME,
       TTES.PASS_MARKS            AS PASSING_SCORE,
       TTES.TOTAL_MARKS           AS TOTAL_MARKS,
       TTED.EXAM_SCORE            AS EXAM_SCORE,
       TTED.REMARKS               AS REMARKS,
       TTED.EVALUATION_DTL_ID     AS EVALUATION_DTL_ID
  FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
       TIMS.TRAINEE_ATTENDANCE           TA,
       TIMS.L_EXAM_TYPE                  TLET,
       TIMS.TRAINEE_EVALUTION_MST        TEM,
       TIMS.TRAINEE_EVALUTION_DTL        TTED,
       TIMS.TRAINEE_EXAM_SCHEDULE        TTES,
       TIMS.TRAINEE                      TT
 WHERE     TT.TRAINEE_ID = TA.TRAINEE_ID
       AND TA.TRAINEE_ID = TTAS.TRAINEE_ID
       AND TA.TRAINEE_ID = TEM.TRAINEE_ID
       AND TA.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TA.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TLET.EXAM_TYPE_ID = TTES.EXAM_TYPE_ID
       AND TEM.SCHEDULE_MST_ID = TA.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TEM.EVALUATION_ID = TTED.EVALUATION_ID
       AND TTES.EXAM_TYPE_ID = TTED.EXAM_TYPE_ID
       AND TA.ATTENDANCE_YN = 'Y'
       AND TA.BATCH_ID = '" . $batch_id . "'
       AND TTES.EXAM_TYPE_ID = '" . $exam_type . "'
UNION ALL
SELECT DISTINCT PE.EMP_ID         AS TRAINEE_ID,
TEM.EVALUATION_ID          AS EVALUATION_ID,
       PE.EMP_NAME                AS TRAINEE_NAME,
       'CPA'                      AS ORGANIZATION_NAME,
       TLET.EXAM_TYPE_ID          AS EXAM_TYPE_ID,
       TLET.EXAM_TYPE_NAME        AS EXAM_TYPE_NAME,
       TTES.PASS_MARKS            AS PASSING_SCORE,
       TTES.TOTAL_MARKS           AS TOTAL_MARKS,
       TTED.EXAM_SCORE            AS EXAM_SCORE,
       TTED.REMARKS               AS REMARKS,
       TTED.EVALUATION_DTL_ID     AS EVALUATION_DTL_ID
  FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS,
       TIMS.TRAINEE_ATTENDANCE           TA,
       TIMS.L_EXAM_TYPE                  TLET,
       TIMS.TRAINEE_EVALUTION_MST        TEM,
       TIMS.TRAINEE_EVALUTION_DTL        TTED,
       TIMS.TRAINEE_EXAM_SCHEDULE        TTES,
       PMIS.EMPLOYEE                     PE
 WHERE     PE.EMP_ID = TA.TRAINEE_ID
       AND TA.TRAINEE_ID = TTAS.TRAINEE_ID
       AND TA.TRAINEE_ID = TEM.TRAINEE_ID
       AND TA.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TA.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TLET.EXAM_TYPE_ID = TTES.EXAM_TYPE_ID
       AND TEM.SCHEDULE_MST_ID = TA.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTAS.SCHEDULE_MST_ID
       AND TEM.SCHEDULE_MST_ID = TTES.SCHEDULE_MST_ID
       AND TEM.EVALUATION_ID = TTED.EVALUATION_ID
       AND TTES.EXAM_TYPE_ID = TTED.EXAM_TYPE_ID
       AND TA.ATTENDANCE_YN = 'Y'
       AND TA.BATCH_ID = '" . $batch_id . "'
       AND TTES.EXAM_TYPE_ID = '" . $exam_type . "'";

        $evaluationEntry = DB::select(DB::raw($querys));
        return $evaluationEntry;
    }

    public function getExamType($batch_id)
    {
        $querys = "SELECT ET.EXAM_TYPE_ID, ET.EXAM_TYPE_NAME
FROM L_EXAM_TYPE ET, TRAINEE_EXAM_SCHEDULE TES
WHERE ET.EXAM_TYPE_ID = TES.EXAM_TYPE_ID
AND TES.BATCH_ID = '" . $batch_id . "'";

        $examType = DB::select(DB::raw($querys));
        return $examType;
    }

    public function getAllTraineeInfo($schedule_mst_id)
    {
        /*$querys = "SELECT
    TTAS.SCHEDULE_MST_ID AS schedule_mst_id,
    TTAS.BATCH_ID AS batch_id,
    TE.TRAINEE_ID AS trainee_id,
    TE.TRAINEE_NAME AS trainee_name,
    TE.ORGANIZATION_NAME AS organization_name
FROM
    TIMS.TRAINEE TE, TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS
    WHERE TE.TRAINEE_ID = TTAS.TRAINEE_ID
        AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id
UNION
SELECT
    TTAS.SCHEDULE_MST_ID AS schedule_mst_id,
    TTAS.BATCH_ID AS batch_id,
    PE.EMP_ID AS trainee_id,
    PE.EMP_NAME AS trainee_name,
    'CPA' AS organization_name
FROM
    PMIS.EMPLOYEE PE, TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS
    WHERE PE.EMP_ID = TTAS.TRAINEE_ID
    AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id
    AND PE.EMP_STATUS_ID = '1'";*/
        $querys = "SELECT
    TTAS.SCHEDULE_MST_ID AS schedule_mst_id,
    TTAS.BATCH_ID AS batch_id,
    TE.TRAINEE_ID AS trainee_id,
    TE.TRAINEE_NAME AS trainee_name,
    TE.ORGANIZATION_NAME AS organization_name
FROM
    TIMS.TRAINEE TE, TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS, TIMS.TRAINEE_FEEDBACK_MST TTFM
    WHERE TE.TRAINEE_ID = TTAS.TRAINEE_ID
    AND TTAS.TRAINEE_ID NOT IN (SELECT TTAS.TRAINEE_ID FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS, TIMS.TRAINEE_FEEDBACK_MST TTFM
     WHERE TTAS.TRAINEE_ID = TTFM.TRAINEE_ID)
    AND TTAS.SCHEDULE_MST_ID = TTFM.SCHEDULE_ID(+)
    AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id
UNION
SELECT
    TTAS.SCHEDULE_MST_ID AS schedule_mst_id,
    TTAS.BATCH_ID AS batch_id,
    PE.EMP_ID AS trainee_id,
    PE.EMP_NAME AS trainee_name,
    'CPA' AS organization_name
FROM
    PMIS.EMPLOYEE PE, TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS, TIMS.TRAINEE_FEEDBACK_MST TTFM
    WHERE PE.EMP_ID = TTAS.TRAINEE_ID
    AND TTAS.TRAINEE_ID NOT IN (SELECT TTAS.TRAINEE_ID FROM TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS, TIMS.TRAINEE_FEEDBACK_MST TTFM
     WHERE TTAS.TRAINEE_ID = TTFM.TRAINEE_ID)
    AND TTAS.SCHEDULE_MST_ID = TTFM.SCHEDULE_ID(+)
    AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id
    AND PE.EMP_STATUS_ID = '1'";

        $AllTraineeInfo = DB::select(DB::raw($querys));
        return $AllTraineeInfo;
    }

    public function getAllBatchWiseFeedBackTrainee($schedule_mst_id)
    {

        $querys = "SELECT
    TTAS.SCHEDULE_MST_ID AS schedule_mst_id,
    TTAS.BATCH_ID AS batch_id,
    TE.TRAINEE_ID AS trainee_id,
    TE.TRAINEE_NAME AS trainee_name,
    TE.ORGANIZATION_NAME AS organization_name
FROM TIMS.TRAINEE TE,
     TIMS.TRAINEE_ASSIGNMENT_SCHEDULE TTAS
    WHERE NOT EXISTS (SELECT TRAINEE_ID FROM TIMS.TRAINEE_FEEDBACK_MST WHERE TRAINEE_ID = TTAS.TRAINEE_ID and SCHEDULE_ID= TTAS.SCHEDULE_MST_ID)
    AND TE.TRAINEE_ID = TTAS.TRAINEE_ID
    AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id
UNION
SELECT TTAS.SCHEDULE_MST_ID     AS schedule_mst_id,
       TTAS.BATCH_ID            AS batch_id,
       PE.EMP_ID                AS trainee_id,
       PE.EMP_NAME              AS trainee_name,
       'CPA'                    AS organization_name
  FROM PMIS.EMPLOYEE                     PE,
       TIMS.TRAINEE_ASSIGNMENT_SCHEDULE  TTAS
 WHERE  NOT EXISTS (SELECT TRAINEE_ID FROM TIMS.TRAINEE_FEEDBACK_MST WHERE TRAINEE_ID = TTAS.TRAINEE_ID and SCHEDULE_ID= TTAS.SCHEDULE_MST_ID)
       AND PE.EMP_ID = TTAS.TRAINEE_ID
       AND PE.EMP_STATUS_ID = '1'
       AND TTAS.SCHEDULE_MST_ID = $schedule_mst_id";

        $AllTraineeInfo = DB::select(DB::raw($querys));
        return $AllTraineeInfo;
    }
}
