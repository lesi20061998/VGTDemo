{{-- 
    Alias layout file - extends master.blade.php
    Nhiều view đang sử dụng 'frontend.layouts.app' nên tạo file này để tương thích
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    {{-- SEO Meta Tags --}}
    <x-seo-meta 
        :title="$seoTitle ?? null"
        :description="$seoDescription ?? null"
        :keywords="$seoKeywords ?? null"
        :image="$seoImage ?? null"
        :url="$seoUrl ?? null"
        :breadcrumbs="$breadcrumbs ?? []"
    />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @php
        // Theme & background từ website config
        $themeColor      = setting_string('theme_color', '#98191F');
        $bgType          = setting_string('bg_type', 'color');
        $bgColor         = setting_string('bg_color', '#f9fafb');
        $bgGradStart     = setting_string('bg_gradient_start', '#4F46E5');
        $bgGradEnd       = setting_string('bg_gradient_end', '#7C3AED');
        $bgGradDir       = setting_string('bg_gradient_direction', 'to right');
        $bgImage         = setting_string('bg_image', '');
        $bgImageSize     = setting_string('bg_image_size', 'cover');
        $bgImagePosition = setting_string('bg_image_position', 'center');
        $bgImageRepeat   = setting_string('bg_image_repeat', 'no-repeat');

        $bodyBgStyle = match($bgType) {
            'gradient' => "background: linear-gradient({$bgGradDir}, {$bgGradStart}, {$bgGradEnd});",
            'image'    => $bgImage
                ? "background-image: url('{$bgImage}'); background-size: {$bgImageSize}; background-position: {$bgImagePosition}; background-repeat: {$bgImageRepeat};"
                : "background-color: {$bgColor};",
            default    => "background-color: {$bgColor};",
        };
    @endphp

    <style>
        :root {
            --theme-color: {{ $themeColor }};
        }
    </style>
    @stack('styles')
</head>
<body style="{{ $bodyBgStyle }}">

    {{-- Custom Body Code --}}
    @if(setting_string('custom_body_code'))
        {!! setting_string('custom_body_code') !!}
    @endif
    
    <!-- Header -->
    @include('frontend.partials.header')
    
    <!-- Main Content -->
    <main role="main" id="main-content">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('frontend.partials.footer')
    
    <!-- Image Protection (Watermark) -->
    @include('frontend.partials.image-protection')
    
    <!-- Popup -->
    @include('frontend.partials.popup')
    
    <!-- Fake Notifications -->
    @include('frontend.partials.fake-notifications')
    
    @stack('scripts')
    
    {{-- Custom Footer Code --}}
    @if(setting_string('custom_footer_code'))
        {!! setting_string('custom_footer_code') !!}
    @endif
</body>
</html>
