@php
    // Helper để lấy giá trị string từ setting (có thể là array hoặc string)
    $getSettingValue = function($key, $default = '') {
        return setting_string($key, $default);
    };
    
    // Lấy màu từ website-config (topbar_bg_color) hoặc fallback
    $topbarBg = $getSettingValue('topbar_bg_color', '#1a1a1a');
    $topbarText = $getSettingValue('topbar_text_color', '#ffffff');
    $headerBg = $getSettingValue('header_bg_color', '#ffffff');
    $headerText = $getSettingValue('header_text_color', '#000000');
    $logo = $getSettingValue('site_logo', '');
    $siteName = $getSettingValue('site_name', 'Website');
    $hotline = $getSettingValue('hotline', '1900 1234');
    
    // Get menus - load tất cả items với children (submenu)
    $topbarMenuId = $getSettingValue('topbar_menu_id', null);
    $navMenuId = $getSettingValue('navigation_menu_id', null);
    
    // Load topbar menu với tất cả items
    $topbarMenu = $topbarMenuId ? \App\Models\Menu::with(['items' => function($query) {
        $query->whereNull('parent_id')
              ->orderBy('order')
              ->with(['children' => function($q) {
                  $q->orderBy('order');
              }]);
    }])->find($topbarMenuId) : null;
    
    // Load navigation menu với tất cả items và children (submenu đa cấp)
    $navMenu = $navMenuId ? \App\Models\Menu::with(['items' => function($query) {
        $query->whereNull('parent_id')
              ->orderBy('order')
              ->with(['children' => function($q) {
                  $q->orderBy('order')
                    ->with(['children' => function($q2) {
                        $q2->orderBy('order');
                    }]);
              }]);
    }])->find($navMenuId) : null;
    
    // Get styles from theme options
    $themeTopbar = setting('theme_option_topbar', []);
    $topbarStyle = is_array($themeTopbar) ? ($themeTopbar['topbar_style'] ?? 'style-1') : 'style-1';
    
    $themeHeader = setting('theme_option_header', []);
    $headerStyle = is_array($themeHeader) ? ($themeHeader['header_style'] ?? 'style-1') : 'style-1';
    
    // Project code for URLs
    $projectCode = request()->route('projectCode');
@endphp

<!-- Topbar -->
@php
    // Check topbar enabled từ website-config HOẶC từ theme_option_topbar
    $topbarEnabled = $getSettingValue('topbar_enabled', 0);
    $isTopbarEnabled = $topbarEnabled == 1 || $topbarEnabled === true || $topbarEnabled === '1' || $topbarEnabled === 'on';
    
    // Nếu đã chọn topbar style trong theme options thì cũng hiển thị
    if (!$isTopbarEnabled && !empty($topbarStyle) && $topbarStyle !== 'none') {
        $isTopbarEnabled = true;
    }
@endphp
@if($isTopbarEnabled)
    @if(view()->exists("frontend.partials.topbars.{$topbarStyle}"))
        @include("frontend.partials.topbars.{$topbarStyle}", [
            'topbarBg' => $topbarBg,
            'topbarText' => $topbarText,
            'topbarMenu' => $topbarMenu
        ])
    @else
        @include('frontend.partials.topbars.style-1', [
            'topbarBg' => $topbarBg,
            'topbarText' => $topbarText,
            'topbarMenu' => $topbarMenu
        ])
    @endif
@endif

<!-- Header -->
@php
    // Fallback to style-1 if selected style doesn't exist
    $headerViewPath = 'frontend.partials.headers.' . $headerStyle;
    if (!view()->exists($headerViewPath)) {
        $headerViewPath = 'frontend.partials.headers.style-1';
    }
@endphp
@include($headerViewPath, [
    'headerBg' => $headerBg,
    'headerText' => $headerText,
    'logo' => $logo,
    'siteName' => $siteName,
    'hotline' => $hotline,
    'navMenu' => $navMenu,
    'projectCode' => $projectCode
])
