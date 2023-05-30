<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingInfo extends Model
{
    protected $table = 'training_info';
    protected $primaryKey = 'training_id';

    /*protected $with = ['training_id'];

    public function training_id()
    {
        return $this->hasMany(TrainingScheduleDtl::class, 'training_id');
    }*/
}
