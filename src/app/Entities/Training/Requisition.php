<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $table = 'training_requsition';
    protected $primaryKey = 'traning_req_id';
}
