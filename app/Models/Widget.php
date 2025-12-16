<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;

class Widget extends Model
{
    use ProjectScoped;

    protected $fillable = ['tenant_id', 'name', 'type', 'area', 'settings', 'sort_order', 'is_active'];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {
            if (session('current_tenant_id')) {
                $query->where('tenant_id', session('current_tenant_id'));
            }
        });

        static::creating(function ($widget) {
            if (!$widget->tenant_id && session('current_tenant_id')) {
                $widget->tenant_id = session('current_tenant_id');
            }
        });
    }
}

