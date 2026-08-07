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
        @include('frontend.layouts.segments.banner-full', [
            'defaultTitle' => 'Bài viết',
            'defaultImage' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200'
        ])
    @endif

    {{-- Content Container --}}
    <div class="container mx-auto px-4 py-12">
        @if(!$hasSidebar)
            @include('frontend.layouts.segments.layout-full', ['contentSection' => 'post-content'])
        @elseif($config['sidebar'] === 'right')
            @include('frontend.layouts.segments.layout-sidebar-right', [
                'contentSection' => 'post-content', 
                'widgetArea' => 'post-sidebar',
                'defaultTitle' => 'Bài viết',
                'defaultImage' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200'
            ])
        @else
            @include('frontend.layouts.segments.layout-sidebar-left', [
                'contentSection' => 'post-content', 
                'widgetArea' => 'post-sidebar',
                'defaultTitle' => 'Bài viết',
                'defaultImage' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200'
            ])
        @endif
    </div>
@endsection
