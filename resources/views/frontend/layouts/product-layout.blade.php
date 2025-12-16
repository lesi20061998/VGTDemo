@php
    $layoutType = get_theme_layout('product');
    $config = get_layout_config($layoutType);
@endphp

@extends('frontend.layouts.master')

@section('content')
    <!-- Banner (if enabled) -->
    @if(isset($layoutType) && ($layoutType === 'sidebar-left-2' || $layoutType === 'sidebar-right-2'))
        <!-- Full Width Banner for Sidebar #2 -->
        <div class="relative bg-cover bg-center py-16" style="background-image: url('{{ $banner ?? 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200' }}')">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
            <div class="container mx-auto px-4 relative z-10">
                <nav class="text-white text-sm mb-2">
                    <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', 'Sản phẩm')</span>
                </nav>
                <h1 class="text-5xl font-bold text-white">@yield('page-title', 'Sản phẩm')</h1>
            </div>
        </div>
    @endif

    <!-- Content Container -->
    <div class="container mx-auto px-4 py-12">
        @if(isset($layoutType) && str_contains($layoutType, 'sidebar-right'))
            <!-- Sidebar Right Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <main class="w-full lg:w-auto lg:col-span-3">
                    @if(isset($layoutType) && $layoutType === 'sidebar-right-1')
                        <!-- Content Banner for Sidebar Right #1 -->
                        <div class="relative bg-cover bg-center py-12 mb-6 rounded-lg" style="background-image: url('{{ $banner ?? 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200' }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg"></div>
                            <div class="px-6 relative z-10">
                                <nav class="text-white text-sm mb-2">
                                    <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', 'Sản phẩm')</span>
                                </nav>
                                <h1 class="text-3xl font-bold text-white">@yield('page-title', 'Sản phẩm')</h1>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white rounded-lg shadow-sm border p-8">
                        @yield('product-content')
                    </div>
                </main>
                <aside class="w-full lg:w-auto lg:col-span-1">
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        @yield('sidebar')
                        {!! render_widgets('product-sidebar') !!}
                        @include('frontend.partials.sidebar-sample')
                    </div>
                </aside>
            </div>
        @else
            <!-- Sidebar Left Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <aside class="w-full lg:w-auto lg:col-span-1">
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        @yield('sidebar')
                        {!! render_widgets('product-sidebar') !!}
                        @include('frontend.partials.sidebar-sample')
                    </div>
                </aside>
                <main class="w-full lg:w-auto lg:col-span-3">
                    @if(isset($layoutType) && $layoutType === 'sidebar-left-1')
                        <!-- Content Banner for Sidebar Left #1 -->
                        <div class="relative bg-cover bg-center py-12 mb-6 rounded-lg" style="background-image: url('{{ $banner ?? 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200' }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg"></div>
                            <div class="px-6 relative z-10">
                                <nav class="text-white text-sm mb-2">
                                    <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', 'Sản phẩm')</span>
                                </nav>
                                <h1 class="text-3xl font-bold text-white">@yield('page-title', 'Sản phẩm')</h1>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white rounded-lg shadow-sm border p-8">
                        @yield('product-content')
                    </div>
                </main>
            </div>
        @endif
    </div>
@endsection
