<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingCalenderMaster extends Model
{
    protected $table = 'training_calender_mst';
    protected $primaryKey = 'calender_id';
}
