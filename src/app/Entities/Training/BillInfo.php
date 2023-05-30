<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use Illuminate\Database\Eloquent\Model;

class BillInfo extends Model
{
    protected $table = 'BILL_INFO';
    protected $primaryKey = 'PAYEE_BILL_ID';
}
