<?php

/**
 * Created by PhpStorm.
 * User: Hossian
 * Date: 4/26/20
 * Time: 01:00 PM
 */

namespace App\Entities\Training;

use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use Illuminate\Database\Eloquent\Model;

class LTrainerAddress extends Model
{
    protected $table = 'l_trainer_address';
    protected $primaryKey = 'address_id';

    public function add_type()
    {
        return $this->belongsTo(LAddress::class, 'address_type_id', 'address_type_id');
    }
    public function div_name()
    {
        return $this->belongsTo(LGeoDivision::class, 'geo_division_id', 'geo_division_id');
    }
    public function dis_name()
    {
        return $this->belongsTo(LGeoDistrict::class, 'geo_district_id', 'geo_district_id');
    }
}
