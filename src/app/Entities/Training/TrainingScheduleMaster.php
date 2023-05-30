<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingScheduleMaster extends Model
{
    protected $table = 'training_schedule_mst';
    protected $primaryKey = 'schedule_id';

    public function training_info()
    {
        return $this->belongsTo(TrainingInfo::class, 'training_id', 'training_id');
    }

    public function training_location()
    {
        return $this->belongsTo(LTrainingLocation::class, 'location_id', 'location_id');
    }

    public function training_calendar()
    {
        return $this->belongsTo(TrainingCalenderMaster::class, 'calender_id', 'calender_id');
    }

}
