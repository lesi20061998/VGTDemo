<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['project_id', 'name', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function locations()
    {
        return $this->hasMany(ShippingZoneLocation::class);
    }
}
