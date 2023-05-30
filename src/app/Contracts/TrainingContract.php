<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/28/20
 * Time: 3:46 PM
 */

namespace App\Contracts;


interface TrainingContract
{
    public function traineeList();
    public function getTraineeInfo($emp_id);
    public function attendanceEntrySearch($batch_id);
    public function attendanceUpdateSearch($batch_id);
    public function getTotalTrainee($batch_id);
    public function evaluationEntrySearch($examDate, $batch_id);
    public function evaluationUpdateSearch($examDate, $batch_id);
    public function getExamType($batch_id);
    public function getAllTraineeInfo($schedule_mst_id);
    public function totalDays($schedule_id);
}
