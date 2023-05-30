<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LTrainingType extends Model
{
    protected $table = 'l_training_type';
    protected $primaryKey = 'training_type_id';
}
