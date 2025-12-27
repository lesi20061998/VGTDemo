<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetTemplate extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'type', 'category', 'description', 'icon', 
        'preview_image', 'config_schema', 'default_settings', 'is_active', 
        'is_premium', 'sort_order', 'template_code', 'template_css', 'template_js'
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
    
    /**
     * Render the template with given settings
     */
    public function render(array $settings = []): string
    {
        if (empty($this->template_code)) {
            return '';
        }
        
        try {
            // Merge default settings with provided settings
            $mergedSettings = array_merge($this->default_settings ?? [], $settings);
            
            // Create a temporary blade file and render it
            $bladeCode = $this->template_code;
            
            // Render using Blade string compiler
            return \Blade::render($bladeCode, [
                'settings' => $mergedSettings,
                'widget' => $this,
                // Add common helpers
                'products' => fn($limit = 10) => \App\Models\Product::take($limit)->get(),
                'posts' => fn($limit = 10) => \App\Models\Post::take($limit)->get(),
                'categories' => fn() => \App\Models\Category::all(),
            ]);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'><strong>Template Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            return '';
        }
    }
}