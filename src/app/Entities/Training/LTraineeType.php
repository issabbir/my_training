<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LTraineeType extends Model
{
    protected $table = 'l_trainee_type';
    protected $primaryKey = 'trainee_type_id';
}
