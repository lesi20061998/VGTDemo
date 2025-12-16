@php
    $topbarBg = setting('topbar_background_color', '#000000');
    $topbarText = setting('topbar_text_color', '#ffffff');
    $headerBg = setting('header_background_color', '#ffffff');
    $headerText = setting('header_text_color', '#000000');
    $logo = setting('site_logo');
    if (is_array($logo)) {
        $logo = $logo['value'] ?? '';
    }
    $topbarMenuId = setting('topbar_menu_id');
    $navMenuId = setting('navigation_menu_id');
    $topbarMenu = $topbarMenuId ? \App\Models\Menu::with(['items' => function($query) {
        $query->whereNull('parent_id')->orderBy('order');
    }])->find($topbarMenuId) : null;
    $navMenu = $navMenuId ? \App\Models\Menu::with(['items' => function($query) {
        $query->whereNull('parent_id')->orderBy('order');
    }])->find($navMenuId) : null;
    
    // Get topbar style from theme_option_topbar
    $themeTopbar = setting('theme_option_topbar', []);
    $topbarStyle = is_array($themeTopbar) ? ($themeTopbar['topbar_style'] ?? 'style-1') : 'style-1';
    
    // Debug
    // dd([
    //     'topbar_enabled' => setting('topbar_enabled'),
    //     'themeTopbar' => $themeTopbar,
    //     'topbarStyle' => $topbarStyle,
    //     'view_exists' => view()->exists('frontend.partials.topbars.' . $topbarStyle)
    // ]);
@endphp

<!-- Topbar -->
@if(setting('topbar_enabled') == 1)
    @if(view()->exists('frontend.partials.topbars.' . $topbarStyle))
        @include('frontend.partials.topbars.' . $topbarStyle, [
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
<header style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        @if($logo)
            <a href="/"><img src="{{ $logo }}" alt="Logo" class="h-12"></a>
        @else
            <a href="/" class="text-2xl font-bold" style="color: {{ $headerText }};">{{ setting('site_name', 'Website') }}</a>
        @endif
        
        <div class="flex gap-4 items-center">
            <button class="search-btn p-2 hover:bg-gray-100 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
            <button class="cart-btn p-2 hover:bg-gray-100 rounded relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </button>
        </div>
    </div>
</header>

<!-- Navigation -->
@if($navMenu && $navMenu->items)
<nav class="bg-gray-100 border-b">
    <div class="container mx-auto px-4">
        <ul class="flex gap-6 py-3">
            @foreach($navMenu->items as $item)
                <li><a href="{{ $item->url }}" class="hover:text-blue-600">{{ $item->title }}</a></li>
            @endforeach
        </ul>
    </div>
</nav>
@endif
