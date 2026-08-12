<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRuleCondition extends Model
{
    protected $fillable = ['shipping_rule_id', 'condition_type', 'operator', 'value_1', 'value_2', 'zone_id'];

    public function rule()
    {
        return $this->belongsTo(ShippingRule::class, 'shipping_rule_id');
    }

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'zone_id');
    }
}
