<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class TraineeEvaluationMaster extends Model
{
    protected $table = 'trainee_evalution_mst';
    protected $primaryKey = 'evaluation_id';
}
