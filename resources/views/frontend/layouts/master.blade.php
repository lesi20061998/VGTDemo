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
    @stack('styles')
</head>
<body class="bg-gray-50">
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
