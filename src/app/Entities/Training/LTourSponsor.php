<?php

/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 10/28/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LTourSponsor extends Model
{
    protected $table = 'l_tour_sponser';
    protected $primaryKey = 'tour_sponser_id';
}
