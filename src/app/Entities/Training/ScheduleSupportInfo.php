<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class ScheduleSupportInfo extends Model
{
    protected $table = 'schedule_support_info';
    protected $primaryKey = 'support_member_id';
}
