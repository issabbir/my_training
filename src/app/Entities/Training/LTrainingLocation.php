<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LTrainingLocation extends Model
{
    protected $table = 'l_training_location';
    protected $primaryKey = 'location_id';
}
