<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;

class Setting extends Model
{
    use ProjectScoped;

    protected $fillable = ['tenant_id', 'key', 'payload', 'group', 'locked'];

    protected $casts = [
        'payload' => 'array',
        'locked' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {
            if (session('current_tenant_id')) {
                $query->where('tenant_id', session('current_tenant_id'));
            }
        });

        static::creating(function ($setting) {
            if (!$setting->tenant_id && session('current_tenant_id')) {
                $setting->tenant_id = session('current_tenant_id');
            }
        });
    }

    public static function set($key, $value, $group = 'general')
    {
        $tenantId = session('current_tenant_id');
        
        return static::updateOrCreate(
            ['key' => $key, 'tenant_id' => $tenantId],
            ['payload' => is_array($value) ? $value : ['value' => $value], 'group' => $group]
        );
    }
}

