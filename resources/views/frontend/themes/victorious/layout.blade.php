<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    {{-- SEO Meta Tags Component --}}
    <x-seo-meta 
        :title="$seoTitle ?? null"
        :description="$seoDescription ?? null"
        :keywords="$seoKeywords ?? null"
        :image="$seoImage ?? null"
        :url="$seoUrl ?? null"
        :breadcrumbs="$breadcrumbs ?? []"
    />
    
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
    
    {{-- Cấu hình Style từ Theme Settings (Phần Input Cấu Hình) --}}
    <style>
        /* Phần cấu hình biến CSS (Màu sắc, Font chữ) */
        :root {
            --theme-primary: {{ setting('theme_primary_color', '#98191F') }};
            --theme-secondary: {{ setting('theme_secondary_color', '#1F2937') }};
            --theme-text: {{ setting('theme_text_color', '#374151') }};
            --theme-bg: {{ setting('theme_bg_color', '#FFFFFF') }};
            --theme-font-base: {!! setting('theme_font_family', "'Poppins', sans-serif") !!};
            --theme-font-heading: {!! setting('theme_heading_font_family', "'Montserrat', sans-serif") !!};
        }

        /* Phần áp dụng Style cho các thẻ cơ bản */
        body {
            font-family: var(--theme-font-base);
            color: var(--theme-text);
            background-color: var(--theme-bg);
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: var(--theme-font-heading);
        }
        
        /* Phần class phụ trợ (Tiện ích) */
        .theme-text-primary { color: var(--theme-primary) !important; }
        .theme-bg-primary { background-color: var(--theme-primary) !important; }
    </style>

    {{-- Tailwind for dynamic header (preflight disabled to not break theme css) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            },
            theme: {
                extend: {
                    colors: {
                        theme: {
                            primary: 'var(--theme-primary)',
                            secondary: 'var(--theme-secondary)',
                        }
                    },
                    fontFamily: {
                        theme: ['var(--theme-font-base)'],
                        heading: ['var(--theme-font-heading)'],
                    }
                }
            }
        }
    <!-- Alpine.js Collapse Plugin & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body>
    @include('frontend.partials.header')
    
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
