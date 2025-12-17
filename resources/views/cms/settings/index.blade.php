@extends('cms.layouts.app')

@section('title', 'Cấu hình hệ thống')
@section('page-title', 'Cấu hình hệ thống')

@section('content')
<div class="mb-6">
    <p class="text-gray-600">Quản lý các cấu hình và thiết lập hệ thống</p>
</div>

<!-- Cấu hình chung -->
<div class="mb-12 bg-white rounded-lg p-6 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b-2 border-gray-200">
        Cấu hình chung
    </h2>
    <div class="grid grid-cols-6 gap-4">
        @foreach($modules->where('permission', 'settings.contact')->merge($modules->where('permission', 'settings.social')) as $module)
        <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md hover:border-red-300 border-2 border-transparent transition-all p-4 h-24 flex items-center">
            <div class="flex items-center space-x-3 w-full">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center group-hover:bg-red-100 flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $module['title'] }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $module['description'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<!-- Quản lý nội dung -->
<div class="mb-12 bg-white rounded-lg p-6 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b-2 border-gray-200">
        Quản lý nội dung
    </h2>
    <div class="grid grid-cols-6 gap-4">
        @foreach($modules->whereIn('permission', ['settings.seo', 'settings.languages', 'settings.fonts', 'settings.watermark', 'settings.toc', 'settings.frontend_features']) as $module)
        <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md hover:border-red-300 border-2 border-transparent transition-all p-4 h-24 flex items-center">
            <div class="flex items-center space-x-3 w-full">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center group-hover:bg-red-100 flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $module['title'] }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $module['description'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<!-- Bán hàng & Thanh toán -->
<div class="mb-12 bg-white rounded-lg p-6 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b-2 border-gray-200">
        Bán hàng & Thanh toán
    </h2>
    <div class="grid grid-cols-6 gap-4">
        @foreach($modules->whereIn('permission', ['settings.payment', 'settings.shipping', 'settings.reviews']) as $module)
        <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md hover:border-red-300 border-2 border-transparent transition-all p-4 h-24 flex items-center">
            <div class="flex items-center space-x-3 w-full">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center group-hover:bg-red-100 flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $module['title'] }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $module['description'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<!-- Marketing & Tương tác -->
<div class="mb-12 bg-white rounded-lg p-6 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b-2 border-gray-200">
        Marketing & Tương tác
    </h2>
    <div class="grid grid-cols-6 gap-4">
        @foreach($modules->whereIn('permission', ['settings.notifications', 'settings.popups', 'settings.fake_notifications', 'settings.forms', 'settings.contact_buttons']) as $module)
        <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md hover:border-red-300 border-2 border-transparent transition-all p-4 h-24 flex items-center">
            <div class="flex items-center space-x-3 w-full">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center group-hover:bg-red-100 flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $module['title'] }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $module['description'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<!-- Hệ thống & Bảo mật -->
<div class="mb-12 bg-white rounded-lg p-6 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b-2 border-gray-200">
        Hệ thống & Bảo mật
    </h2>
    <div class="grid grid-cols-6 gap-4">
        @foreach($modules->whereIn('permission', ['settings.permissions', 'settings.logs', 'settings.analytics', 'settings.redirects']) as $module)
        <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md hover:border-red-300 border-2 border-transparent transition-all p-4 h-24 flex items-center">
            <div class="flex items-center space-x-3 w-full">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center group-hover:bg-red-100 flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $module['title'] }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $module['description'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<!-- Công cụ nâng cao -->
<div class="mb-12 bg-white rounded-lg p-6 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b-2 border-gray-200">
        Công cụ nâng cao
    </h2>
    <div class="grid grid-cols-6 gap-4">
        @foreach($modules->whereIn('permission', ['settings.ai']) as $module)
        <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md hover:border-red-300 border-2 border-transparent transition-all p-4 h-24 flex items-center">
            <div class="flex items-center space-x-3 w-full">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center group-hover:bg-red-100 flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $module['title'] }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $module['description'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

@if($modules->isEmpty())
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
    <svg class="w-16 h-16 text-yellow-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
    </svg>
    <h3 class="text-xl font-semibold text-yellow-900 mb-2">Không có quyền truy cập</h3>
    <p class="text-yellow-700">Bạn không có quyền truy cập vào bất kỳ module cấu hình nào. Vui lòng liên hệ quản trị viên để được cấp quyền.</p>
</div>
@endif
@endsection