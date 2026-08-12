<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCarrier extends Model
{
    protected $fillable = ['project_id', 'name', 'code', 'type', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function rateVersions()
    {
        return $this->hasMany(ShippingRateVersion::class);
    }

    public function activeRateVersion()
    {
        return $this->hasOne(ShippingRateVersion::class)->where('is_active', true);
    }
}
