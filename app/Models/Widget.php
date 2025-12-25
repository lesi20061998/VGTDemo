<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $fillable = ['name', 'type', 'area', 'settings', 'sort_order', 'is_active', 'variant', 'metadata'];

    protected $casts = [
        'settings' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get rendered content for this widget
     */
    public function getRenderedContent(): string
    {
        return \App\Widgets\WidgetRegistry::render(
            $this->type, 
            $this->settings ?? [], 
            $this->variant ?? 'default'
        );
    }

    /**
     * Validate widget settings against metadata
     */
    public function validateSettings(): bool
    {
        try {
            $widgetClass = \App\Widgets\WidgetRegistry::get($this->type);
            if ($widgetClass) {
                $widget = new $widgetClass($this->settings ?? [], $this->variant ?? 'default');
                return $widget->validateSettings();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get preview HTML for admin interface
     */
    public function getPreview(): string
    {
        return \App\Widgets\WidgetRegistry::getPreview(
            $this->type, 
            $this->settings ?? [], 
            $this->variant ?? 'default'
        );
    }

    /**
     * Get widget metadata
     */
    public function getWidgetMetadata(): ?array
    {
        return \App\Widgets\WidgetRegistry::getConfig($this->type);
    }
}

