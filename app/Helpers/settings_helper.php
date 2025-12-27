<?php

use App\Services\SettingsService;

if (!function_exists('get_config')) {
    function get_config($key, $default = null)
    {
        return SettingsService::getInstance()->get($key, $default);
    }
}

if (!function_exists('set_config')) {
    function set_config($key, $value, $group = null, $locked = false)
    {
        return SettingsService::getInstance()->set($key, $value, $group, $locked);
    }
}

if (!function_exists('get_theme_layout')) {
    function get_theme_layout($type = 'page')
    {
        // Use setting() helper which respects project context
        $data = setting('theme_option_layout', []);
        
        // Ensure $data is an array
        if (!is_array($data)) {
            $data = [];
        }
        
        $layoutKey = $type . '_layout';
        return $data[$layoutKey] ?? 'full-width';
    }
}

if (!function_exists('get_theme_option')) {
    /**
     * Get theme option value from specific tab
     * @param string $tab Tab name (header, topbar, navigation, layout, etc.)
     * @param string|null $key Specific key within the tab, or null to get all
     * @param mixed $default Default value if not found
     * @return mixed
     */
    function get_theme_option($tab, $key = null, $default = null)
    {
        $data = setting("theme_option_{$tab}", []);
        
        if (!is_array($data)) {
            return $default;
        }
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
}

if (!function_exists('get_layout_config')) {
    function get_layout_config($layoutType)
    {
        $layouts = [
            'full-width' => [
                'sidebar' => false,
                'banner' => false,
                'content_class' => 'w-full',
                'container_class' => 'container mx-auto'
            ],
            'full-width-banner' => [
                'sidebar' => false,
                'banner' => true,
                'content_class' => 'w-full',
                'container_class' => 'container mx-auto'
            ],
            'sidebar-left' => [
                'sidebar' => 'left',
                'banner' => false,
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'container_class' => 'container mx-auto flex flex-col lg:flex-row gap-6'
            ],
            'sidebar-left-1' => [
                'sidebar' => 'left',
                'banner' => true,
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'container_class' => 'container mx-auto flex flex-col lg:flex-row gap-6'
            ],
            'sidebar-left-2' => [
                'sidebar' => 'left',
                'banner' => true,
                'banner_style' => 'style-2',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'container_class' => 'container mx-auto flex flex-col lg:flex-row gap-6'
            ],
            'sidebar-right' => [
                'sidebar' => 'right',
                'banner' => false,
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'container_class' => 'container mx-auto flex flex-col lg:flex-row gap-6'
            ],
            'sidebar-right-1' => [
                'sidebar' => 'right',
                'banner' => true,
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'container_class' => 'container mx-auto flex flex-col lg:flex-row gap-6'
            ],
            'sidebar-right-2' => [
                'sidebar' => 'right',
                'banner' => true,
                'banner_style' => 'style-2',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'container_class' => 'container mx-auto flex flex-col lg:flex-row gap-6'
            ]
        ];
        
        return $layouts[$layoutType] ?? $layouts['full-width'];
    }
}

