<?php

if (! function_exists('setting')) {
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                set_config($k, $v);
            }

            return true;
        }

        $value = get_config($key, $default);

        // Return arrays for complex settings like watermark
        if (is_array($value)) {
            return $value;
        }

        return $value;
    }
}

if (! function_exists('setting_string')) {
    function setting_string($key, $default = '')
    {
        $value = setting($key, $default);

        return is_string($value) ? $value : (string) ($value ?: $default);
    }
}

if (! function_exists('trans_db')) {
    function trans_db($key, $replace = [], $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translations = setting('translations', []);

        foreach ($translations as $trans) {
            if ($trans['key'] === $key) {
                $text = $trans['values'][$locale] ?? $key;
                if (! empty($text)) {
                    foreach ($replace as $search => $value) {
                        $text = str_replace(":$search", $value, $text);
                    }

                    return $text;
                }
            }
        }

        return $key;
    }
}

if (! function_exists('current_project')) {
    function current_project()
    {
        return request()->attributes->get('project');
    }
}

if (! function_exists('can_project')) {
    function can_project($module, $action = 'view')
    {
        $project = current_project();
        if (! $project) {
            return true; // Default admin panel
        }

        return $project->hasPermission($module, $action);
    }
}

if (! function_exists('render_menu')) {
    function render_menu($location = 'header')
    {
        $menu = \App\Models\Menu::where('location', $location)
            ->where('is_active', true)
            ->with(['items' => function ($query) {
                $query->whereNull('parent_id')->with('children')->orderBy('order');
            }])
            ->first();

        if (! $menu || $menu->items->isEmpty()) {
            return '';
        }

        return view('components.menu-simple', compact('menu'))->render();
    }
}

if (! function_exists('menu_item_url')) {
    function menu_item_url($item)
    {
        if ($item->url) {
            return $item->url;
        }

        if ($item->linkable) {
            switch ($item->linkable_type) {
                case 'App\\Models\\Post':
                    return route('frontend.page', $item->linkable->slug ?? $item->linkable->id);
                case 'App\\Models\\ProductCategory':
                    return route('frontend.category', $item->linkable->slug ?? $item->linkable->id);
                default:
                    return '#';
            }
        }

        return '#';
    }
}
