<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingScheduleDtl extends Model
{
    protected $table = 'training_schedule_dtl';
    protected $primaryKey = 'schedule_dtl_id';

    public function l_trainer_info()
    {
        return $this->belongsTo(LTrainer::class, 'trainer_id', 'trainer_id');
    }

    public function prev_trainer_info()
    {
        return $this->belongsTo(LTrainer::class, 'previous_trainer_id', 'trainer_id');
    }
}
