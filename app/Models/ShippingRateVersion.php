<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRateVersion extends Model
{
    protected $fillable = ['shipping_carrier_id', 'version_name', 'start_date', 'end_date', 'is_active'];

    public function carrier()
    {
        return $this->belongsTo(ShippingCarrier::class, 'shipping_carrier_id');
    }

    public function rules()
    {
        return $this->hasMany(ShippingRule::class);
    }
}
