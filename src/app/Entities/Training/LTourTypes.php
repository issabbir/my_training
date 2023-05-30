<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LTourTypes extends Model
{
    protected $table = 'tims.l_tour_types';
    public $timestamps = false;
    protected $primaryKey = 'tour_type_id';
}
