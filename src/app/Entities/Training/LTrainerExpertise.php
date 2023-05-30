<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LTrainerExpertise extends Model
{
    protected $table = 'l_trainer_expertise';
    protected $primaryKey = 'trainer_exp_id';

    public function l_exper()
    {
        return $this->belongsTo(LExpertise::class, 'expertise_id', 'expertise_id');
    }
}
