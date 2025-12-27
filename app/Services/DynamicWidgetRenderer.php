<?php

namespace App\Services;

use App\Models\Widget;
use App\Models\WidgetTemplate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class DynamicWidgetRenderer
{
    /**
     * Render a widget by its ID
     */
    public function renderById(int $widgetId): string
    {
        $widget = Widget::find($widgetId);
        if (!$widget) {
            return '';
        }

        return $this->render($widget);
    }

    /**
     * Render a widget instance
     */
    public function render(Widget $widget): string
    {
        // First try code-based widget
        if (\App\Widgets\WidgetRegistry::exists($widget->type)) {
            return $widget->getRenderedContent();
        }

        // Try database template
        $template = WidgetTemplate::where('type', $widget->type)->first();
        if (!$template) {
            return $this->renderError("Widget template '{$widget->type}' not found");
        }

        return $this->renderFromTemplate($template, $widget->settings ?? []);
    }

    /**
     * Render widget from database template
     */
    public function renderFromTemplate(WidgetTemplate $template, array $settings): string
    {
        // First priority: Use template_code if available (custom Blade/PHP code from admin)
        if (!empty($template->template_code)) {
            return $this->renderFromCode($template, $settings);
        }
        
        // Second priority: Check if there's a blade view for this template type
        $viewName = "widgets.dynamic.{$template->type}";
        
        if (View::exists($viewName)) {
            return view($viewName, [
                'settings' => $settings,
                'template' => $template,
            ])->render();
        }

        // Fallback to generic renderer
        return $this->renderGeneric($template, $settings);
    }
    
    /**
     * Render widget from template_code (Blade/PHP code stored in database)
     */
    protected function renderFromCode(WidgetTemplate $template, array $settings): string
    {
        try {
            // Merge default settings with provided settings
            $mergedSettings = array_merge($template->default_settings ?? [], $settings);
            
            // Prepare helper functions
            $products = fn($limit = 10) => \App\Models\Product::take($limit)->get();
            $posts = fn($limit = 10) => \App\Models\Post::take($limit)->get();
            $categories = fn() => \App\Models\Category::all();
            
            // Render the Blade code
            $html = Blade::render($template->template_code, [
                'settings' => $mergedSettings,
                'widget' => $template,
                'products' => $products,
                'posts' => $posts,
                'categories' => $categories,
            ]);
            
            // Inject CSS if available
            if (!empty($template->template_css)) {
                $html = "<style>{$template->template_css}</style>" . $html;
            }
            
            // Inject JS if available
            if (!empty($template->template_js)) {
                $html .= "<script>{$template->template_js}</script>";
            }
            
            return $html;
        } catch (\Exception $e) {
            return $this->renderError("Template render error: " . $e->getMessage());
        }
    }

    /**
     * Generic renderer for templates without custom views
     */
    protected function renderGeneric(WidgetTemplate $template, array $settings): string
    {
        $fields = $template->config_schema['fields'] ?? [];
        
        $html = '<div class="widget widget-' . e($template->type) . '">';
        
        foreach ($fields as $field) {
            $value = $settings[$field['name']] ?? $field['default'] ?? '';
            
            if (empty($value)) {
                continue;
            }

            $html .= $this->renderFieldValue($field, $value);
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render a single field value
     */
    protected function renderFieldValue(array $field, mixed $value): string
    {
        $type = $field['type'] ?? 'text';
        
        return match ($type) {
            'image' => $this->renderImage($value, $field),
            'gallery' => $this->renderGallery($value, $field),
            'textarea' => $this->renderTextarea($value, $field),
            'url' => $this->renderUrl($value, $field),
            'repeatable' => $this->renderRepeatable($value, $field),
            default => $this->renderText($value, $field),
        };
    }

    protected function renderText(mixed $value, array $field): string
    {
        return '<div class="widget-field widget-field-text">' . e($value) . '</div>';
    }

    protected function renderTextarea(mixed $value, array $field): string
    {
        return '<div class="widget-field widget-field-textarea">' . nl2br(e($value)) . '</div>';
    }

    protected function renderImage(mixed $value, array $field): string
    {
        if (empty($value)) {
            return '';
        }
        return '<div class="widget-field widget-field-image"><img src="' . e($value) . '" alt="' . e($field['label'] ?? '') . '" class="max-w-full h-auto"></div>';
    }

    protected function renderGallery(mixed $value, array $field): string
    {
        if (!is_array($value) || empty($value)) {
            return '';
        }
        
        $html = '<div class="widget-field widget-field-gallery grid grid-cols-3 gap-2">';
        foreach ($value as $image) {
            $html .= '<img src="' . e($image) . '" alt="" class="w-full h-auto">';
        }
        $html .= '</div>';
        
        return $html;
    }

    protected function renderUrl(mixed $value, array $field): string
    {
        if (empty($value)) {
            return '';
        }
        return '<div class="widget-field widget-field-url"><a href="' . e($value) . '" class="text-blue-600 hover:underline">' . e($value) . '</a></div>';
    }

    protected function renderRepeatable(mixed $value, array $field): string
    {
        if (!is_array($value) || empty($value)) {
            return '';
        }
        
        $subFields = $field['fields'] ?? [];
        $html = '<div class="widget-field widget-field-repeatable space-y-2">';
        
        foreach ($value as $item) {
            $html .= '<div class="repeatable-item p-2 border rounded">';
            foreach ($subFields as $subField) {
                $subValue = $item[$subField['name']] ?? '';
                if (!empty($subValue)) {
                    $html .= $this->renderFieldValue($subField, $subValue);
                }
            }
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    protected function renderError(string $message): string
    {
        if (config('app.debug')) {
            return '<div class="widget-error bg-red-50 border border-red-200 text-red-600 p-4 rounded">' . e($message) . '</div>';
        }
        return '';
    }

    /**
     * Render all widgets for an area
     */
    public function renderArea(string $area): string
    {
        $widgets = Widget::where('area', $area)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $html = '';
        foreach ($widgets as $widget) {
            $html .= $this->render($widget);
        }

        return $html;
    }

    /**
     * Render a custom widget template (called from WidgetRegistry)
     */
    public function renderCustomWidget(WidgetTemplate $template, array $settings): string
    {
        return $this->renderFromTemplate($template, $settings);
    }
}
