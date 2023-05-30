<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LCourse extends Model
{
    protected $table = 'l_course';
    protected $primaryKey = 'course_id';
}
