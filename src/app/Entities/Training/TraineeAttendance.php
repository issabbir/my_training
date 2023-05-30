<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class TraineeAttendance extends Model
{
    protected $table = 'trainee_attendance';
    protected $primaryKey = 'attendance_id';
}


