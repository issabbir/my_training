<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class ForeignTour extends Model
{
    protected $table = 'foreign_tour';//FOREIGN_TOUR
    protected $primaryKey = 'f_tour_id';
}
