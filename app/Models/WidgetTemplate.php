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
    
    /**
     * Render the template from blade file with given settings
     */
    public function render(array $settings = []): string
    {
        $bladePath = resource_path("views/widgets/custom/{$this->type}/view.blade.php");
        
        if (!\File::exists($bladePath)) {
            return '';
        }
        
        try {
            // Merge default settings with provided settings
            $mergedSettings = array_merge($this->default_settings ?? [], $settings);
            
            return view("widgets.custom.{$this->type}.view", [
                'settings' => $mergedSettings,
                'widget' => $this,
                // Add common helpers
                'products' => fn($limit = 10) => \App\Models\Product::take($limit)->get(),
                'posts' => fn($limit = 10) => \App\Models\Post::take($limit)->get(),
                'categories' => fn() => \App\Models\Category::all(),
            ])->render();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'><strong>Template Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            return '';
        }
    }
    
    /**
     * Get CSS content from file
     */
    public function getCss(): string
    {
        // First check folder structure: views/widgets/custom/{type}/style.css
        $cssPath = resource_path("views/widgets/custom/{$this->type}/style.css");
        if (\File::exists($cssPath)) {
            return \File::get($cssPath);
        }
        
        // Fallback: check for direct file views/widgets/custom/{type}.css
        $directCssPath = resource_path("views/widgets/custom/{$this->type}.css");
        if (\File::exists($directCssPath)) {
            return \File::get($directCssPath);
        }
        
        return '';
    }
    
    /**
     * Get JS content from file
     */
    public function getJs(): string
    {
        // First check folder structure: views/widgets/custom/{type}/script.js
        $jsPath = resource_path("views/widgets/custom/{$this->type}/script.js");
        if (\File::exists($jsPath)) {
            return \File::get($jsPath);
        }
        
        // Fallback: check for direct file views/widgets/custom/{type}.js
        $directJsPath = resource_path("views/widgets/custom/{$this->type}.js");
        if (\File::exists($directJsPath)) {
            return \File::get($directJsPath);
        }
        
        return '';
    }
    
    /**
     * Get CSS style tag with inline content
     */
    public function getCssTag(): string
    {
        $css = $this->getCss();
        if (!empty(trim($css))) {
            return "<style>{$css}</style>";
        }
        return '';
    }
    
    /**
     * Get JS script tag with inline content
     */
    public function getJsTag(): string
    {
        $js = $this->getJs();
        if (!empty(trim($js))) {
            return "<script>{$js}</script>";
        }
        return '';
    }
}