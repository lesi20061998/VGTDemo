<?php

namespace App\Models;

use App\Widgets\WidgetRegistry;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $fillable = ['name', 'type', 'area', 'settings', 'sort_order', 'is_active', 'variant', 'metadata', 'tenant_id'];

    protected $casts = [
        'settings' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public function getSettingsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && ! empty($value)) {
            while (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_string($decoded))) {
                    $value = $decoded;
                } else {
                    break;
                }
            }
            if (is_array($value)) {
                return $value;
            }
        }

        return [];
    }

    /**
     * Get rendered content for this widget
     */
    public function getRenderedContent(): string
    {
        return WidgetRegistry::render(
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
            $widgetClass = WidgetRegistry::get($this->type);
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
        return WidgetRegistry::getPreview(
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
        return WidgetRegistry::getConfig($this->type);
    }
}
