<?php

use App\Models\Widget;
use Illuminate\Support\Facades\Cache;

if (!function_exists('render_widget_area')) {
    function render_widget_area($areaKey)
    {
        return Cache::remember("widget_area_{$areaKey}", 3600, function () use ($areaKey) {
            $widgets = Widget::where('area_key', $areaKey)
                ->where('active', true)
                ->orderBy('order')
                ->get();
            
            $html = '';
            
            foreach ($widgets as $widget) {
                try {
                    $widgetClass = $widget->widget_class;
                    
                    if (class_exists($widgetClass)) {
                        $widgetInstance = new $widgetClass($widget->config ?? []);
                        $html .= $widgetInstance->render();
                    }
                } catch (\Exception $e) {
                    \Log::error("Widget render error: " . $e->getMessage());
                }
            }
            
            return $html;
        });
    }
}

if (!function_exists('clear_widget_cache')) {
    function clear_widget_cache($areaKey = null)
    {
        if ($areaKey) {
            Cache::forget("widget_area_{$areaKey}");
        } else {
            Cache::flush();
        }
    }
}

if (!function_exists('render_widgets')) {
    function render_widgets($area)
    {
        $widgets = Widget::where('area', $area)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        $html = '';
        foreach ($widgets as $widget) {
            $html .= \App\Widgets\WidgetRegistry::render($widget->type, $widget->settings ?? []);
        }
        
        return $html;
    }
}

