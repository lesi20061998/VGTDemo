<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetTemplate extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'type', 'category', 'description', 'icon', 
        'preview_image', 'config_schema', 'default_settings', 'is_active', 
        'is_premium', 'sort_order'
    ];

    protected $casts = [
        'config_schema' => 'array',
        'default_settings' => 'array',
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {
            if (session('current_tenant_id')) {
                $query->where('tenant_id', session('current_tenant_id'));
            }
        });

        static::creating(function ($template) {
            if (!$template->tenant_id && session('current_tenant_id')) {
                $template->tenant_id = session('current_tenant_id');
            }
        });
    }

    public function widgets()
    {
        return $this->hasMany(Widget::class, 'type', 'type');
    }
}