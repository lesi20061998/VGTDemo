<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    protected $fillable = ['shipping_rate_version_id', 'name', 'priority', 'action_type', 'fee', 'is_surcharge', 'status'];

    public function rateVersion()
    {
        return $this->belongsTo(ShippingRateVersion::class, 'shipping_rate_version_id');
    }

    public function conditions()
    {
        return $this->hasMany(ShippingRuleCondition::class);
    }
}
