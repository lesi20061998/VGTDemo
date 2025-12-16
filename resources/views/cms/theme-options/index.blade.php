@extends('cms.layouts.app')

@section('title', 'Theme Options')
@section('page-title', 'Theme Options')

@push('head')
<link rel="preconnect" href="{{ asset('') }}">
<link rel="dns-prefetch" href="{{ asset('') }}">
<style>
img[data-cache-img]{will-change:transform;content-visibility:auto;}
.aspect-video,.aspect-square{contain:layout style paint;}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/theme-options-cache.js') }}" defer></script>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Tabs -->
    <div class="border-b">
        <nav class="flex">
            <a href="?tab=layout" class="px-6 py-4 {{ in_array($tab, ['layout', 'post-category', 'banner']) ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                Layout
            </a>
            <a href="?tab=header" class="px-6 py-4 {{ $tab === 'header' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                Header
            </a>
            <a href="?tab=navigation" class="px-6 py-4 {{ $tab === 'navigation' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                Navigation
            </a>
            <a href="?tab=topbar" class="px-6 py-4 {{ $tab === 'topbar' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                Top Bar
            </a>
            <a href="?tab=heading" class="px-6 py-4 {{ $tab === 'heading' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                Heading
            </a>
        </nav>
    </div>
    
    @if(in_array($tab, ['layout', 'post-category', 'banner']))
    <!-- Sub Tabs for Layout -->
    <div class="bg-gray-50 border-b">
        <nav class="flex px-6">
            <a href="?tab=layout" class="px-4 py-3 {{ $tab === 'layout' ? 'bg-white border-t-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                Layout
            </a>
            <a href="?tab=post-category" class="px-4 py-3 {{ $tab === 'post-category' ? 'bg-white border-t-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                Post Category
            </a>
            <a href="?tab=banner" class="px-4 py-3 {{ $tab === 'banner' ? 'bg-white border-t-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                Banner
            </a>
        </nav>
    </div>
    @endif

    <!-- Content -->
    <div class="p-6">
        <form method="POST" action="{{ route('project.admin.theme-options.update', ['projectCode' => request()->segment(1)]) }}?tab={{ $tab }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" value="{{ $tab }}">

            @if($tab === 'layout')
                @include('cms.theme-options.tabs.layout')
            @elseif($tab === 'header')
                @include('cms.theme-options.tabs.header')
            @elseif($tab === 'navigation')
                @include('cms.theme-options.tabs.navigation')
            @elseif($tab === 'topbar')
                @include('cms.theme-options.tabs.topbar')
            @elseif($tab === 'heading')
                @include('cms.theme-options.tabs.heading')
            @elseif($tab === 'post-category')
                @include('cms.theme-options.tabs.post-category')
            @elseif($tab === 'banner')
                @include('cms.theme-options.tabs.banner')
            @endif

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
            </div>
        </form>
    </div>
</div>
@endsection
