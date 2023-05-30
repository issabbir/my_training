<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class TraineeAssignmentSchedule extends Model
{
    protected $table = 'trainee_assignment_schedule';
    protected $primaryKey = 'assignment_id';
}


