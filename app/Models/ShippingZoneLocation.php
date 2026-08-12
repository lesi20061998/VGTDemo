<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZoneLocation extends Model
{
    protected $fillable = ['shipping_zone_id', 'province_code', 'district_code', 'ward_code'];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}
