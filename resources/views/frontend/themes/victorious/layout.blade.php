<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="{{ asset('themes/victorious/favicon.svg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Victorious Cruise')</title>
    <meta name="description" content="@yield('description', 'Victorious Cruise - Luxury Cruise Experience')">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Petrona:wght@100..900&display=swap" rel="stylesheet">
    
    {{-- Libraries --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    
    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/victorious/css/style.css') }}">
    
    @stack('styles')
</head>
<body>
    @include('frontend.themes.victorious.partials.header')
    
    <main class="p-top">
        @yield('content')
        
        {{-- Render widgets for homepage-main area --}}
        @if(isset($widgetArea))
            {!! render_widgets($widgetArea) !!}
        @endif
    </main>
    
    @include('frontend.themes.victorious.partials.footer')
    
    {{-- Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="{{ asset('themes/victorious/js/main.js') }}"></script>
    <script src="{{ asset('themes/victorious/js/slider.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
