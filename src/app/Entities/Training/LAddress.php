<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class LAddress extends Model
{
    protected $table = 'l_address_type';
    protected $primaryKey = 'address_type_id';
}
