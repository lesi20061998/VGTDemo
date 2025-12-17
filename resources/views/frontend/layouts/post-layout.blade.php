@php
    $layoutType = get_theme_layout('post');
    $config = get_layout_config($layoutType);
    $hasSidebar = $config['sidebar'] ?? false;
    $hasBanner = $config['banner'] ?? false;
    $bannerStyle = $config['banner_style'] ?? null;
@endphp

@extends('frontend.layouts.master')

@section('content')
    {{-- Full Width Banner (style 2 - above container) --}}
    @if($hasBanner && $bannerStyle === 'style-2')
        <div class="relative bg-cover bg-center py-16" style="background-image: url('{{ $banner ?? 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200' }}')">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
            <div class="container mx-auto px-4 relative z-10">
                <nav class="text-white text-sm mb-2">
                    <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', 'Bài viết')</span>
                </nav>
                <h1 class="text-5xl font-bold text-white">@yield('page-title', 'Bài viết')</h1>
            </div>
        </div>
    @endif

    {{-- Full Width Banner (no sidebar) --}}
    @if(!$hasSidebar && $hasBanner && $bannerStyle !== 'style-2')
        <div class="relative bg-cover bg-center py-16" style="background-image: url('{{ $banner ?? 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200' }}')">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
            <div class="container mx-auto px-4 relative z-10">
                <nav class="text-white text-sm mb-2">
                    <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', 'Bài viết')</span>
                </nav>
                <h1 class="text-5xl font-bold text-white">@yield('page-title', 'Bài viết')</h1>
            </div>
        </div>
    @endif

    {{-- Content Container --}}
    <div class="container mx-auto px-4 py-12">
        @if(!$hasSidebar)
            {{-- Full Width Layout --}}
            <main class="w-full">
                <div class="bg-white rounded-lg shadow-sm border p-8">
                    @yield('post-content')
                </div>
            </main>
        @elseif($config['sidebar'] === 'right')
            {{-- Sidebar Right Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <main class="lg:col-span-3">
                    @if($hasBanner && $bannerStyle !== 'style-2')
                        <div class="relative bg-cover bg-center py-12 mb-6 rounded-lg" style="background-image: url('{{ $banner ?? 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200' }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg"></div>
                            <div class="px-6 relative z-10">
                                <nav class="text-white text-sm mb-2">
                                    <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', 'Bài viết')</span>
                                </nav>
                                <h1 class="text-3xl font-bold text-white">@yield('page-title', 'Bài viết')</h1>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white rounded-lg shadow-sm border p-8">
                        @yield('post-content')
                    </div>
                </main>
                <aside class="lg:col-span-1">
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        @yield('sidebar')
                        {!! render_widgets('post-sidebar') !!}
                    </div>
                </aside>
            </div>
        @else
            {{-- Sidebar Left Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <aside class="lg:col-span-1">
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        @yield('sidebar')
                        {!! render_widgets('post-sidebar') !!}
                    </div>
                </aside>
                <main class="lg:col-span-3">
                    @if($hasBanner && $bannerStyle !== 'style-2')
                        <div class="relative bg-cover bg-center py-12 mb-6 rounded-lg" style="background-image: url('{{ $banner ?? 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200' }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg"></div>
                            <div class="px-6 relative z-10">
                                <nav class="text-white text-sm mb-2">
                                    <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', 'Bài viết')</span>
                                </nav>
                                <h1 class="text-3xl font-bold text-white">@yield('page-title', 'Bài viết')</h1>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white rounded-lg shadow-sm border p-8">
                        @yield('post-content')
                    </div>
                </main>
            </div>
        @endif
    </div>
@endsection
