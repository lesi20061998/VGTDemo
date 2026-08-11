@extends('cms.layouts.app')

@section('title', 'Quản lý Widget')
@section('page-title', 'Widget Manager')

@php
    $projectCode = request()->route('projectCode');
    $baseUrl     = $projectCode ? "/{$projectCode}/admin" : '/admin';

    $widgetAreas = [
        'homepage-main' => ['label' => 'Trang chủ', 'icon' => 'home'],
        'sidebar'        => ['label' => 'Sidebar', 'icon' => 'view-list'],
        'footer'         => ['label' => 'Footer', 'icon' => 'template'],
        'blog-sidebar'   => ['label' => 'Blog Sidebar', 'icon' => 'document-text'],
    ];
@endphp

@section('content')
<x-media-picker-modal />

{{-- Toast Notification --}}
<div id="wm-toast" class="fixed top-5 right-5 z-[9999] hidden">
    <div id="wm-toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium text-white min-w-[280px]">
        <svg id="wm-toast-icon" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
        <span id="wm-toast-msg"></span>
    </div>
</div>

{{-- ================================================================ --}}
{{-- CONFIG MODAL — Full-screen: Left = Form | Right = Live Preview   --}}
{{-- ================================================================ --}}
<div id="config-drawer" class="fixed inset-0 z-[100] hidden flex-col" style="background:rgba(15,23,42,0.7);">
    <div class="absolute inset-4 bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden">

        {{-- ── Top bar ──────────────────────────────────────────── --}}
        <div class="flex items-center gap-3 px-5 py-3 bg-gray-900 text-white flex-shrink-0">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm leading-tight truncate" id="drawer-title">Cấu hình Widget</p>
                <p class="text-xs text-gray-400 leading-tight truncate" id="drawer-subtitle"></p>
            </div>
            {{-- Viewport buttons --}}
            <div class="flex items-center gap-1 bg-gray-800 rounded-lg p-1 flex-shrink-0">
                <button data-vp-width="375" class="modal-vp-btn text-gray-400 hover:text-white flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition" title="Mobile (375px)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Mobile
                </button>
                <button data-vp-width="768" class="modal-vp-btn text-gray-400 hover:text-white flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition" title="Tablet (768px)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Tablet
                </button>
                <button data-vp-width="1280" class="modal-vp-btn text-white bg-blue-600 flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition" title="Desktop (1280px)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Desktop
                </button>
            </div>
            <button id="btn-close-drawer" class="ml-1 p-2 hover:bg-gray-700 rounded-lg transition flex-shrink-0" title="Đóng (ESC)">
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Main: Form (left) + Preview (right) ─────────────── --}}
        <div class="flex flex-1 overflow-hidden">

            {{-- Left: Config Form --}}
            <div class="w-80 flex-shrink-0 flex flex-col border-r border-gray-200 bg-gray-50">
                <div class="flex-1 overflow-y-auto px-5 py-4" id="drawer-body">
                    <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                        <svg class="w-5 h-5 animate-spin mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-sm">Đang tải form...</span>
                    </div>
                </div>
                {{-- Action bar --}}
                <div class="flex-shrink-0 px-4 py-3 border-t border-gray-200 bg-white flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <button id="btn-cancel-drawer"
                                class="flex-1 py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition">
                            Huỷ
                        </button>
                        <button id="btn-save-config"
                                class="flex-[2] flex items-center justify-center gap-2 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Lưu cấu hình
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Live Preview --}}
            <div class="flex-1 flex flex-col bg-[#f1f5f9] overflow-hidden relative">
                
                {{-- Toolbar inside preview --}}
                <div class="absolute top-0 inset-x-0 z-10 flex items-center gap-3 px-4 py-2 bg-white/80 backdrop-blur border-b border-gray-200 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Live Preview</span>
                    <span class="text-xs text-gray-500 bg-gray-200 px-2 py-0.5 rounded-md font-medium" id="modal-vp-size-label">
                        Desktop (1280px)
                    </span>
                    <div class="flex-1"></div>
                    <span class="text-xs text-gray-400">Preview có thể khác một chút so với thực tế</span>
                </div>

                {{-- Scrollable Container --}}
                <div class="flex-1 overflow-auto p-6 pt-16 flex items-start justify-center">
                    
                    {{-- Device Wrapper --}}
                    <div id="modal-preview-wrapper" class="relative transition-all duration-300" style="width: 1280px; min-height: 400px;">
                        
                        {{-- Loading Overlay --}}
                        <div id="modal-preview-loading" class="absolute inset-0 bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center z-20 rounded-xl shadow-xl hidden">
                            <svg class="w-8 h-8 text-blue-500 animate-spin mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-600">Đang render preview...</span>
                        </div>

                        {{-- Frame & Content --}}
                        <iframe id="modal-preview-content" class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 w-full" style="height: 800px;" scrolling="auto"></iframe>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


{{-- Page Heading --}}
<div class="mb-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-400 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Quản trị</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span>Giao diện</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-600 font-medium">Widget Manager</span>
    </nav>

    {{-- Title row --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Widget Manager</h1>
            <p class="text-sm text-gray-500 mt-1">Thêm, cấu hình và xem trước các block nội dung cho từng khu vực website.</p>
        </div>
        <button id="btn-clear-cache" class="flex-shrink-0 px-3 py-2 text-xs text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200 transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Xoá cache
        </button>
    </div>

    {{-- Stats bar --}}
    @php
        $totalActive = collect($existingWidgets)->sum(fn($ws) => $ws->count());
        $totalAvailable = array_sum(array_map('count', $availableWidgets));
        $totalAreas = count($widgetAreas);
    @endphp
    <div class="flex flex-wrap items-center gap-3 mt-4">
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
            <div class="w-6 h-6 bg-blue-50 rounded-md flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 leading-none">Khu vực</p>
                <p class="text-sm font-bold text-gray-800 leading-tight">{{ $totalAreas }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
            <div class="w-6 h-6 bg-emerald-50 rounded-md flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 leading-none">Đang active</p>
                <p class="text-sm font-bold text-gray-800 leading-tight" id="stat-active">{{ $totalActive }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
            <div class="w-6 h-6 bg-purple-50 rounded-md flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 leading-none">Widget có sẵn</p>
                <p class="text-sm font-bold text-gray-800 leading-tight">{{ $totalAvailable }}</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 text-xs text-gray-400 ml-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Di chuột vào widget bên phải để xem trước giao diện
        </div>
    </div>
</div>

{{-- Main 3-Column Layout --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

    {{-- COL 1: Widget Areas (5/12) --}}
    <div class="xl:col-span-5 space-y-4">
        @foreach($widgetAreas as $areaKey => $areaInfo)
            @php $areaWidgets = $existingWidgets[$areaKey] ?? collect([]); @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Area Header --}}
                <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border-b">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($areaInfo['icon'] === 'home')
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        @elseif($areaInfo['icon'] === 'view-list')
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        @elseif($areaInfo['icon'] === 'template')
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm text-gray-800">{{ $areaInfo['label'] }}</h3>
                        <p class="text-xs text-gray-400">{{ $areaKey }}</p>
                    </div>
                    <span class="bg-blue-50 text-blue-600 text-xs font-medium px-2.5 py-0.5 rounded-full" id="badge-{{ $areaKey }}">
                        {{ $areaWidgets->count() }} widget
                    </span>
                </div>

                {{-- Widget List --}}
                <div class="widget-area-list divide-y divide-gray-50" id="area-list-{{ $areaKey }}" data-area="{{ $areaKey }}">
                    @forelse($areaWidgets as $w)
                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50/80 transition group widget-row"
                             data-id="{{ $w['id'] ?? '' }}"
                             data-type="{{ $w['type'] }}"
                             data-area="{{ $areaKey }}"
                             data-name="{{ $w['name'] }}">
                            <div class="w-1 h-8 bg-blue-400 rounded-full flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $w['name'] }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $w['type'] }}</p>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                <button class="btn-open-config p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition"
                                        data-id="{{ $w['id'] ?? '' }}"
                                        data-type="{{ $w['type'] }}"
                                        data-name="{{ $w['name'] }}"
                                        data-settings="{{ htmlspecialchars(json_encode($w['settings'] ?? []), ENT_QUOTES) }}"
                                        data-variant="{{ $w['variant'] ?? 'default' }}"
                                        title="Cấu hình">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button class="btn-remove-widget p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition"
                                        data-id="{{ $w['id'] ?? '' }}"
                                        data-area="{{ $areaKey }}"
                                        title="Xoá">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center empty-area-placeholder" id="empty-{{ $areaKey }}">
                            <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <p class="text-xs text-gray-400">Chưa có widget. Chọn từ danh sách bên phải để thêm.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- COL 2: Available Widgets (4/12) --}}
    <div class="xl:col-span-4 sticky top-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="px-4 py-3 bg-gray-50 border-b flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 class="font-semibold text-sm text-gray-800 flex-1">Widget có sẵn</h3>
                <span class="bg-blue-100 text-blue-700 text-xs font-medium px-2 py-0.5 rounded-full">
                    {{ array_sum(array_map('count', $availableWidgets)) }}
                </span>
            </div>

            {{-- Search --}}
            <div class="px-3 py-2.5 border-b">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" id="widgetSearch"
                           placeholder="Tìm widget..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            {{-- Area Selector --}}
            <div class="px-3 py-2.5 border-b bg-blue-50/40">
                <label class="flex items-center gap-1.5 text-xs font-medium text-gray-600 mb-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Thêm vào khu vực:
                </label>
                <select id="targetArea" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white outline-none">
                    @foreach($widgetAreas as $areaKey => $areaInfo)
                        <option value="{{ $areaKey }}">{{ $areaInfo['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Widget Categories List --}}
            <div class="overflow-y-auto max-h-[calc(100vh-380px)]" id="widgetTemplatesList">
                @forelse($availableWidgets as $category => $categoryWidgets)
                    <div class="widget-category" data-category="{{ $category }}">
                        {{-- Category toggle button --}}
                        <button type="button"
                                data-cat="{{ $category }}"
                                class="btn-toggle-category w-full flex items-center justify-between px-4 py-2.5 text-left hover:bg-gray-50 transition border-b border-gray-100">
                            <span class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200 category-arrow" id="arrow-{{ $category }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                            </span>
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">{{ count($categoryWidgets) }}</span>
                        </button>

                        {{-- Category items --}}
                        <div class="category-content hidden divide-y divide-gray-50" id="category-{{ $category }}">
                            @foreach($categoryWidgets as $widget)
                                <div class="widget-template flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition group border-l-2 border-transparent hover:border-blue-400"
                                     data-type="{{ $widget['type'] }}"
                                     data-name="{{ $widget['metadata']['name'] ?? ($widget['name'] ?? $widget['type']) }}"
                                     data-cat="{{ $category }}">
                                    <div class="w-7 h-7 bg-gray-100 rounded-md flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition">
                                        <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate group-hover:text-blue-700 transition">
                                            {{ $widget['metadata']['name'] ?? ($widget['name'] ?? $widget['type']) }}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate">{{ $widget['type'] }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-sm">Không có widget nào khả dụng</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    {{-- COL 3: Widget Preview (3/12) --}}
    <div class="xl:col-span-3 sticky top-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Preview Header --}}
            <div class="px-4 py-3 bg-gray-50 border-b flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <h3 class="font-semibold text-sm text-gray-800 flex-1">Xem trước</h3>
                <span class="text-xs text-gray-400" id="preview-widget-type"></span>
            </div>

            {{-- Preview Body --}}
            <div id="preview-body" class="min-h-[200px] overflow-auto">
                {{-- Empty state --}}
                <div id="preview-empty" class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Xem trước giao diện</p>
                    <p class="text-xs text-gray-400 mt-1">Di chuột hoặc click vào một widget ở danh sách bên trái để xem trước</p>
                </div>

                {{-- Loading state (hidden by default) --}}
                <div id="preview-loading" class="hidden flex flex-col items-center justify-center py-12 px-4">
                    <svg class="w-6 h-6 text-blue-400 animate-spin mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <p class="text-xs text-gray-400">Đang tải xem trước...</p>
                </div>

                {{-- Preview content (hidden by default) --}}
                <div id="preview-content" class="hidden">
                    {{-- Widget name badge --}}
                    <div class="px-3 py-2 bg-blue-50 border-b border-blue-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        <span class="text-xs font-medium text-blue-700" id="preview-widget-name"></span>
                    </div>
                    {{-- Scaled iframe preview --}}
                    <div class="relative bg-gray-50 overflow-hidden" style="height: 420px;">
                        <div id="preview-scale-wrapper" style="width: 1280px; transform-origin: top left;">
                            <div id="preview-html" class="w-full"></div>
                        </div>
                    </div>
                    {{-- Add button --}}
                    <div class="px-3 py-2.5 border-t bg-gray-50">
                        <button id="btn-add-previewed-widget"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Thêm widget này vào khu vực
                        </button>
                    </div>
                </div>

                {{-- Error state (hidden by default) --}}
                <div id="preview-error" class="hidden flex flex-col items-center justify-center py-12 px-4 text-center">
                    <svg class="w-8 h-8 text-red-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Widget này chưa có preview</p>
                    <p class="text-xs text-gray-400 mt-1" id="preview-error-msg"></p>
                </div>
            </div>
        </div>
    {{-- Global Media Picker Modal --}}
    <x-media-picker-modal />
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const BASE_URL = '{{ $baseUrl }}';
    const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ── SAFE JSON ────────────────────────────────────────────────────────
    // Safely parse JSON — throws descriptive error if server returns HTML
    function safeJson(r) {
        var ct = r.headers.get('content-type') || '';
        if (! ct.includes('application/json') && ! ct.includes('text/json')) {
            return r.text().then(function (body) {
                var hint = '';
                if (r.status === 419) { hint = ' (CSRF token hết hạn — thử reload trang)'; }
                else if (r.status === 401 || r.status === 302) { hint = ' (chưa đăng nhập)'; }
                else if (r.status === 500) { hint = ' (lỗi server — kiểm tra log)'; }
                throw new Error('HTTP ' + r.status + hint + '. Body: ' + body.substring(0, 120));
            });
        }
        return r.json();
    }

    // ── SAFE JSON (ok-only routes: DELETE, PUT) ───────────────────────────
    // For routes that return 204/200 on success and JSON only on error
    function safeJsonOrOk(r) {
        if (r.ok || r.redirected) { return Promise.resolve({ success: true }); }
        return safeJson(r);
    }

    let drawerWidgetId   = null;
    let drawerWidgetType = null;
    let drawerWidgetArea = null;
    let previewedType    = null;
    let previewedName    = null;
    let previewTimer     = null;
    let modalPreviewTimer = null;

    // ── TOAST ──────────────────────────────────────────────────────
    const TOAST_ICONS = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        info:    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    };
    const TOAST_BG = { success: 'bg-emerald-500', error: 'bg-red-500', info: 'bg-blue-500' };

    function showToast(msg, type) {
        type = type || 'success';
        const t   = document.getElementById('wm-toast');
        const inn = document.getElementById('wm-toast-inner');
        const ico = document.getElementById('wm-toast-icon');
        const tx  = document.getElementById('wm-toast-msg');
        inn.className = 'flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium text-white min-w-[280px] ' + (TOAST_BG[type] || TOAST_BG.success);
        ico.innerHTML = TOAST_ICONS[type] || TOAST_ICONS.success;
        tx.textContent = msg;
        t.classList.remove('hidden');
        clearTimeout(window._wmt);
        window._wmt = setTimeout(function () { t.classList.add('hidden'); }, 3500);
    }

    // ── HELPERS ────────────────────────────────────────────────────
    function escAttr(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function updateBadge(area) {
        var list  = document.getElementById('area-list-' + area);
        var badge = document.getElementById('badge-' + area);
        if (!list || !badge) { return; }
        badge.textContent = list.querySelectorAll('.widget-row').length + ' widget';
    }

    function checkEmptyArea(area) {
        var list = document.getElementById('area-list-' + area);
        if (!list || list.querySelector('.widget-row')) { return; }
        var empty = document.createElement('div');
        empty.id = 'empty-' + area;
        empty.className = 'px-4 py-8 text-center empty-area-placeholder';
        empty.innerHTML =
            '<svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>' +
            '</svg>' +
            '<p class="text-xs text-gray-400">Chưa có widget. Chọn từ danh sách bên phải để thêm.</p>';
        list.appendChild(empty);
    }

    function buildWidgetRow(area, widget) {
        var row = document.createElement('div');
        row.className = 'flex items-center gap-3 px-4 py-3 hover:bg-gray-50/80 transition group widget-row';
        row.dataset.id   = widget.id || '';
        row.dataset.type = widget.type;
        row.dataset.area = area;
        row.dataset.name = widget.name;
        row.innerHTML =
            '<div class="w-1 h-8 bg-blue-400 rounded-full flex-shrink-0"></div>' +
            '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-medium text-gray-800 truncate">' + escAttr(widget.name) + '</p>' +
                '<p class="text-xs text-gray-400 truncate">' + escAttr(widget.type) + '</p>' +
            '</div>' +
            '<div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">' +
                '<button class="btn-open-config p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition"' +
                    ' data-id="' + escAttr(widget.id || '') + '"' +
                    ' data-type="' + escAttr(widget.type) + '"' +
                    ' data-name="' + escAttr(widget.name) + '"' +
                    ' data-settings="' + escAttr(JSON.stringify(widget.settings || {})) + '"' +
                    ' data-variant="' + escAttr(widget.variant || 'default') + '"' +
                    ' title="Cấu hình">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>' +
                    '</svg></button>' +
                '<button class="btn-remove-widget p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition"' +
                    ' data-id="' + escAttr(widget.id || '') + '"' +
                    ' data-area="' + escAttr(area) + '"' +
                    ' title="Xoá">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>' +
                    '</svg></button>' +
            '</div>';
        return row;
    }

    // ── ADD WIDGET ─────────────────────────────────────────────────
    function addWidget(type, name) {
        var area = document.getElementById('targetArea').value;
        if (!area) { showToast('Vui lòng chọn khu vực', 'error'); return; }

        fetch(BASE_URL + '/widgets', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ name: name, type: type, area: area, sort_order: 999, is_active: true })
        })
        .then(safeJson)
        .then(function (data) {
            if (data.success === false) { showToast(data.message || 'Lỗi khi thêm widget', 'error'); return; }
            var id = (data.widget && data.widget.id) ? data.widget.id : (data.id || '');
            showToast('Đã thêm widget vào khu vực', 'success');
            var list = document.getElementById('area-list-' + area);
            if (list) {
                var empty = document.getElementById('empty-' + area);
                if (empty) { empty.remove(); }
                list.appendChild(buildWidgetRow(area, { id: id, type: type, name: name, settings: {}, variant: 'default' }));
                updateBadge(area);
            }
        })
        .catch(function () { showToast('Lỗi kết nối server', 'error'); });
    }

    // ── REMOVE WIDGET ──────────────────────────────────────────────
    function removeWidget(btn) {
        var id   = btn.dataset.id;
        var area = btn.dataset.area;
        if (!id) { showToast('Không tìm thấy ID widget', 'error'); return; }
        if (!confirm('Xoá widget này khỏi khu vực?')) { return; }

        fetch(BASE_URL + '/widgets/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(safeJsonOrOk)
        .then(function (data) {
            if (data && data.success === false) { showToast(data.message || 'Lỗi', 'error'); return; }
            var row = btn.closest('.widget-row');
            if (row) { row.remove(); }
            updateBadge(area);
            checkEmptyArea(area);
            showToast('Đã xoá widget', 'success');
        })
        .catch(function () { showToast('Lỗi kết nối', 'error'); });
    }

    // ── CONDITIONAL FIELDS ─────────────────────────────────────────
    function initConditionalFields() {
        var conditionalFields = document.querySelectorAll('[data-show-if]');
        
        conditionalFields.forEach(function(fieldWrapper) {
            var showIf = JSON.parse(fieldWrapper.dataset.showIf);
            
            // Get the controlling field
            Object.keys(showIf).forEach(function(controlFieldName) {
                var controlField = document.querySelector('[name="' + controlFieldName + '"]');
                if (!controlField) return;
                
                // Check initial state
                checkConditional(fieldWrapper, controlField, showIf);
                
                // Listen to changes
                controlField.addEventListener('change', function() {
                    checkConditional(fieldWrapper, controlField, showIf);
                    // Trigger preview reload when conditional field changes
                    clearTimeout(window.previewTimeout);
                    window.previewTimeout = setTimeout(function() {
                        loadModalPreview();
                    }, 300);
                });
            });
        });
    }
    
    function checkConditional(targetWrapper, controlField, showIf) {
        var controlFieldName = controlField.getAttribute('name');
        var expectedValue = showIf[controlFieldName];
        var currentValue = controlField.value;
        
        if (currentValue === expectedValue) {
            targetWrapper.style.display = '';
        } else {
            targetWrapper.style.display = 'none';
        }
    }
    
    function initFormInputListeners() {
        var drawerBody = document.getElementById('drawer-body');
        if (!drawerBody || drawerBody._hasInputListeners) return;
        drawerBody._hasInputListeners = true;

        ['input', 'change', 'keyup'].forEach(function(evtName) {
            drawerBody.addEventListener(evtName, function(e) {
                clearTimeout(window.previewTimeout);
                window.previewTimeout = setTimeout(function() {
                    loadModalPreview();
                }, 200);
            });
        });
    }

    // ── OPEN CONFIG DRAWER ─────────────────────────────────────────
    function openConfig(btn) {
        drawerWidgetId   = btn.dataset.id;
        drawerWidgetType = btn.dataset.type;
        var row = btn.closest('.widget-row');
        drawerWidgetArea = row ? row.dataset.area : null;

        var settings = {};
        var rawSettings = btn.dataset.settings || '{}';
        for (var i = 0; i < 3; i++) {
            if (typeof rawSettings === 'string' && rawSettings.trim().length > 0) {
                try {
                    var parsed = JSON.parse(rawSettings);
                    if (typeof parsed === 'object' && parsed !== null) {
                        settings = parsed;
                        break;
                    } else {
                        rawSettings = parsed;
                    }
                } catch (e) {
                    break;
                }
            } else if (typeof rawSettings === 'object' && rawSettings !== null) {
                settings = rawSettings;
                break;
            }
        }

        document.getElementById('drawer-title').textContent    = btn.dataset.name || drawerWidgetType;
        document.getElementById('drawer-subtitle').textContent = drawerWidgetType;
        document.getElementById('drawer-body').innerHTML =
            '<div class="flex items-center justify-center h-32 text-gray-400">' +
            '<svg class="w-5 h-5 animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>' +
            '<span class="text-sm">Đang tải form...</span></div>';

        // Reset modal preview
        var previewContent = document.getElementById('modal-preview-content');
        if (previewContent && previewContent.contentWindow) {
            var doc = previewContent.contentWindow.document;
            var styles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style')).map(function(el) { return el.outerHTML; }).join('');
            doc.open();
            doc.write('<html><head>' + styles + '<style>body{margin:0;padding:0;background:transparent;}</style></head><body>' +
                '<div class="flex flex-col items-center justify-center py-20 px-4 text-center text-gray-400">' +
                '<svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>' +
                '</svg><p class="text-sm font-medium text-gray-500">Đang tải preview...</p></div></body></html>');
            doc.close();
        }

        var drawer = document.getElementById('config-drawer');
        drawer.classList.remove('hidden');
        drawer.classList.add('flex'); // Make it flex-col

        var params = new URLSearchParams({ type: drawerWidgetType, settings: JSON.stringify(settings) });
        fetch(BASE_URL + '/widgets/fields?' + params.toString(), { headers: { 'Accept': 'application/json' } })
        .then(safeJson)
        .then(function (data) {
            if (!data.success) { showToast(data.message || 'Không tải được form', 'error'); return; }
            document.getElementById('drawer-body').innerHTML = data.form_html || '<p class="text-gray-500 text-sm p-4">Widget này không có trường cấu hình.</p>';
            if (window.Alpine) {
                try { window.Alpine.initTree(document.getElementById('drawer-body')); } catch(e) {}
            }
            document.dispatchEvent(new CustomEvent('widget-form-loaded'));
            // Init conditional fields
            initConditionalFields();
            // Init form input listeners for live preview
            initFormInputListeners();
            // Auto load preview after form is loaded
            loadModalPreview();
        })
        .catch(function () {
            document.getElementById('drawer-body').innerHTML = '<p class="text-red-500 text-sm p-4">Lỗi khi tải form cấu hình.</p>';
        });
    }

    function closeDrawer() {
        var drawer = document.getElementById('config-drawer');
        drawer.classList.add('hidden');
        drawer.classList.remove('flex');
        drawerWidgetId = null;
        drawerWidgetType = null;
        drawerWidgetArea = null;
        iframeInitialized = false; // Reset iframe state
    }

    // ── FORM SETTINGS & MODAL PREVIEW ──────────────────────────────
    function getFormSettings() {
        var settings = {};
        var drawerBody = document.getElementById('drawer-body');
        if (!drawerBody) return settings;

        // First, trigger Alpine to sync all x-model bindings
        if (window.Alpine) {
            window.Alpine.nextTick(function() {
                // Alpine has updated all bindings
            });
        }

        drawerBody.querySelectorAll('input, textarea, select').forEach(function (input) {
            if (!input.name) { return; }

            // Skip hidden conditional fields (check if parent wrapper is hidden)
            var wrapper = input.closest('.widget-field') || input.closest('[data-show-if]');
            if (wrapper && wrapper.style.display === 'none') {
                return; // Skip this field, it's hidden by conditional logic
            }

            // For Alpine.js bound fields, try to get the value from Alpine component
            var alpineComponent = input.closest('[x-data]');
            if (alpineComponent && alpineComponent.tagName !== 'BODY') {
                var alpineData = window.Alpine ? window.Alpine.$data(alpineComponent) : (alpineComponent._x_dataStack ? alpineComponent._x_dataStack[0] : null);
                if (alpineData) {
                    if (alpineData.imageUrl !== undefined && (input.type === 'hidden' || input.type === 'text')) {
                        settings[input.name] = alpineData.imageUrl || input.value;
                        return;
                    }
                    if (alpineData.currentValue !== undefined && input.type === 'hidden') {
                        settings[input.name] = alpineData.currentValue || input.value;
                        return;
                    }
                }
            }

            if (input.type === 'checkbox') {
                settings[input.name] = input.checked;
            } else if (input.type === 'radio') {
                if (input.checked) { settings[input.name] = input.value; }
            } else {
                settings[input.name] = input.value;
            }
        });

        return settings;
    }

    var iframeInitialized = false;

    function adjustIframeHeight(iframe) {
        if (!iframe || !iframe.contentWindow) return;
        var doc = iframe.contentWindow.document;
        if (!doc || !doc.body) return;

        // Get actual content height
        var height = Math.max(
            doc.body.scrollHeight,
            doc.body.offsetHeight,
            doc.documentElement.scrollHeight,
            doc.documentElement.offsetHeight,
            250
        );

        // Add some padding
        iframe.style.height = (height + 20) + 'px';
    }

    function loadModalPreview() {
        if (!drawerWidgetType) return;
        var settings = getFormSettings();
        var loadingOverlay = document.getElementById('modal-preview-loading');
        if (loadingOverlay) loadingOverlay.classList.remove('hidden');

        fetch(BASE_URL + '/widgets/preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ type: drawerWidgetType, settings: settings, variant: 'default' })
        })
        .then(safeJson)
        .then(function (data) {
            var iframe = document.getElementById('modal-preview-content');
            if (!iframe || !iframe.contentWindow) return;
            var doc = iframe.contentWindow.document;

            if (! data.success || ! data.preview) {
                doc.open();
                doc.write('<div style="padding: 1.5rem; color: #ef4444; font-family: sans-serif;">Lỗi preview: ' + (data.message || 'Lỗi không xác định') + '</div>');
                doc.close();
                iframeInitialized = false;
            } else {
                // Re-init iframe document if needed
                var wrapper = doc.getElementById('preview-inner-wrapper');
                if (!iframeInitialized || !wrapper) {
                    var tailwindScript = '<script src="https://cdn.tailwindcss.com"><\/script>';
                    var styles = tailwindScript + Array.from(document.querySelectorAll('head link[rel="stylesheet"], head style')).map(function(el) { return el.outerHTML; }).join('');
                    doc.open();
                    doc.write('<html><head>' + styles + '<style>body{margin:0;padding:0;background:transparent;} [x-cloak] { display: none !important; }</style></head><body><div id="preview-inner-wrapper" style="overflow: hidden; min-height: 250px;"></div></body></html>');
                    doc.close();
                    iframeInitialized = true;
                    wrapper = doc.getElementById('preview-inner-wrapper');
                }

                // Update inner content
                if (wrapper) {
                    wrapper.innerHTML = data.preview;
                    
                    // Re-execute scripts
                    var scripts = wrapper.querySelectorAll('script');
                    scripts.forEach(function(oldScript) {
                        var newScript = doc.createElement('script');
                        Array.from(oldScript.attributes).forEach(function(attr) { newScript.setAttribute(attr.name, attr.value); });
                        newScript.appendChild(doc.createTextNode(oldScript.innerHTML));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                    
                    // Adjust iframe height
                    adjustIframeHeight(iframe);
                    
                    // Auto-resize iframe when content changes
                    var resizeObserver = new MutationObserver(function() {
                        adjustIframeHeight(iframe);
                    });
                    resizeObserver.observe(wrapper, { childList: true, subtree: true, attributes: true });
                }
            }
        })
        .catch(function (err) {
            console.error('Preview fetch error:', err);
            var iframe = document.getElementById('modal-preview-content');
            if (iframe && iframe.contentWindow) {
                var doc = iframe.contentWindow.document;
                if (!iframeInitialized) {
                    doc.open();
                    doc.write('<div style="padding: 1.5rem; color: #ef4444; font-family: sans-serif;">Lỗi kết nối khi tải preview: ' + err.message + '</div>');
                    doc.close();
                } else {
                    var wrapper = doc.getElementById('preview-inner-wrapper');
                    if (wrapper) wrapper.innerHTML = '<div style="padding: 1.5rem; color: #ef4444; font-family: sans-serif;">Lỗi kết nối khi tải preview: ' + err.message + '</div>';
                }
            }
        })
        .finally(function() {
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
        });
    }

    // ── SAVE CONFIG ────────────────────────────────────────────────
    function saveWidgetConfig() {
        if (!drawerWidgetId) { showToast('Không có widget nào được chọn', 'error'); return; }
        var settings = getFormSettings();

        var btn      = document.getElementById('btn-save-config');
        var origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML =
            '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Đang lưu...';

        fetch(BASE_URL + '/widgets/' + drawerWidgetId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('drawer-title').textContent,
                type: drawerWidgetType,
                area: drawerWidgetArea || 'homepage-main',
                settings: JSON.stringify(settings),
                is_active: true
            })
        })
        .then(safeJson)
        .then(function (data) {
            showToast('Đã lưu cấu hình widget', 'success');
            // Update dataset on config button and row so reopening drawer reflects new state
            var configBtn = document.querySelector('.btn-open-config[data-id="' + drawerWidgetId + '"]');
            if (!configBtn && drawerWidgetId) {
                configBtn = document.querySelector('.widget-row[data-id="' + drawerWidgetId + '"] .btn-open-config');
            }
            if (configBtn && data.widget) {
                var updatedSettings = data.widget.settings || settings;
                if (typeof updatedSettings === 'string') {
                    try { updatedSettings = JSON.parse(updatedSettings); } catch(e) {}
                }
                configBtn.dataset.settings = JSON.stringify(updatedSettings);
                configBtn.dataset.name = data.widget.name || document.getElementById('drawer-title').textContent;
                var row = configBtn.closest('.widget-row');
                if (row) {
                    row.dataset.name = configBtn.dataset.name;
                    var rowName = row.querySelector('.font-medium');
                    if (rowName) rowName.textContent = configBtn.dataset.name;
                }
            }
            closeDrawer();
        })
        .catch(function () { showToast('Lỗi khi lưu', 'error'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = origHtml; });
    }

    // ── CLEAR CACHE ────────────────────────────────────────────────
    function clearCache() {
        fetch(BASE_URL + '/widgets/clear-cache', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(safeJson)
        .then(function () { showToast('Đã xoá cache widget', 'success'); })
        .catch(function () { showToast('Lỗi khi xoá cache', 'error'); });
    }

    // ── CATEGORY TOGGLE ────────────────────────────────────────────
    function toggleCategory(cat) {
        var el    = document.getElementById('category-' + cat);
        var arrow = document.getElementById('arrow-' + cat);
        if (!el) { return; }
        el.classList.toggle('hidden');
        if (arrow) { arrow.style.transform = el.classList.contains('hidden') ? '' : 'rotate(90deg)'; }
    }

    // ── SEARCH ─────────────────────────────────────────────────────
    function filterWidgets(q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('.widget-template').forEach(function (el) {
            var show = !q || (el.dataset.name || '').toLowerCase().includes(q) || (el.dataset.type || '').toLowerCase().includes(q);
            el.style.display = show ? '' : 'none';
        });
        document.querySelectorAll('.widget-category').forEach(function (cat) {
            var visible = cat.querySelectorAll('.widget-template:not([style*="display: none"])');
            cat.style.display = visible.length ? '' : 'none';
            if (q && visible.length) {
                var catKey = cat.dataset.category;
                var el     = document.getElementById('category-' + catKey);
                var arrow  = document.getElementById('arrow-' + catKey);
                if (el) { el.classList.remove('hidden'); if (arrow) { arrow.style.transform = 'rotate(90deg)'; } }
            }
        });
    }

    // ── PREVIEW ────────────────────────────────────────────────────
    function showPreviewState(state) {
        var states = ['preview-empty', 'preview-loading', 'preview-content', 'preview-error'];
        states.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.classList.toggle('hidden', id !== state); }
        });
    }

    function rescalePreview() {
        var wrapper   = document.getElementById('preview-scale-wrapper');
        var container = wrapper ? wrapper.parentElement : null;
        if (!wrapper || !container) { return; }
        var scale = container.offsetWidth / 1280;
        wrapper.style.transform = 'scale(' + scale + ')';
        wrapper.style.height    = Math.ceil(wrapper.scrollHeight) + 'px';
        container.style.height  = Math.ceil(wrapper.scrollHeight * scale) + 'px';
    }

    function loadPreview(type, name) {
        if (previewedType === type) { return; }
        previewedType = type;
        previewedName = name;

        document.getElementById('preview-widget-type').textContent = type;
        showPreviewState('preview-loading');

        fetch(BASE_URL + '/widgets/preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ type: type, settings: [], variant: 'default' })
        })
        .then(safeJson)
        .then(function (data) {
            if (! data.success || ! data.preview) {
                document.getElementById('preview-error-msg').textContent = data.message || '';
                showPreviewState('preview-error');
                return;
            }
            document.getElementById('preview-widget-name').textContent = name;
            document.getElementById('preview-html').innerHTML = data.preview;
            showPreviewState('preview-content');
            // Scale after content renders
            setTimeout(rescalePreview, 50);
        })
        .catch(function () {
            document.getElementById('preview-error-msg').textContent = 'Không thể kết nối server';
            showPreviewState('preview-error');
        });
    }

    // ── EVENT DELEGATION ───────────────────────────────────────────
    document.addEventListener('click', function (e) {
        // Open config
        var configBtn = e.target.closest('.btn-open-config');
        if (configBtn) { openConfig(configBtn); return; }

        // Remove widget
        var removeBtn = e.target.closest('.btn-remove-widget');
        if (removeBtn) { removeWidget(removeBtn); return; }

        // Category toggle
        var catBtn = e.target.closest('.btn-toggle-category');
        if (catBtn) { toggleCategory(catBtn.dataset.cat); return; }

        // Widget template click → add + load preview
        var tpl = e.target.closest('.widget-template');
        if (tpl) { addWidget(tpl.dataset.type, tpl.dataset.name); loadPreview(tpl.dataset.type, tpl.dataset.name); return; }

        // Drawer close
        if (e.target.id === 'drawer-backdrop' || e.target.closest('#btn-close-drawer') || e.target.closest('#btn-cancel-drawer')) {
            closeDrawer();
            return;
        }
    });

    // Hover on widget template → preview (debounced 300ms)
    document.addEventListener('mouseover', function (e) {
        var tpl = e.target.closest('.widget-template');
        if (!tpl) { return; }
        clearTimeout(previewTimer);
        var type = tpl.dataset.type;
        var name = tpl.dataset.name;
        previewTimer = setTimeout(function () { loadPreview(type, name); }, 300);
    });

    // Add previewed widget button
    document.getElementById('btn-add-previewed-widget').addEventListener('click', function () {
        if (previewedType && previewedName) {
            addWidget(previewedType, previewedName);
        }
    });

    // Rescale on window resize
    window.addEventListener('resize', rescalePreview);

    // Search input
    document.getElementById('widgetSearch').addEventListener('input', function () {
        filterWidgets(this.value);
    });

    // Save config button
    var btnSaveConfig = document.getElementById('btn-save-config');
    if (btnSaveConfig) btnSaveConfig.addEventListener('click', saveWidgetConfig);

    // Clear cache button
    var btnClearCache = document.getElementById('btn-clear-cache');
    if (btnClearCache) btnClearCache.addEventListener('click', clearCache);

    // Auto-refresh modal preview on input change
    document.getElementById('drawer-body').addEventListener('input', function (e) {
        if (e.target.matches('input, textarea, select')) {
            clearTimeout(modalPreviewTimer);
            modalPreviewTimer = setTimeout(loadModalPreview, 400);
        }
    });
    document.getElementById('drawer-body').addEventListener('change', function (e) {
        if (e.target.matches('input[type="checkbox"], input[type="radio"], select')) {
            clearTimeout(modalPreviewTimer);
            modalPreviewTimer = setTimeout(loadModalPreview, 100);
        }
    });

    // Viewport switcher
    document.querySelectorAll('.modal-vp-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.modal-vp-btn').forEach(function(b) {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('text-gray-400');
            });
            this.classList.remove('text-gray-400');
            this.classList.add('bg-blue-600', 'text-white');
            
            // Set wrapper width
            var w = this.dataset.vpWidth;
            var wrapper = document.getElementById('modal-preview-wrapper');
            var label = document.getElementById('modal-vp-size-label');
            if (wrapper) wrapper.style.width = w + 'px';
            if (label) {
                if (w === '375') label.textContent = 'Mobile (375px)';
                else if (w === '768') label.textContent = 'Tablet (768px)';
                else label.textContent = 'Desktop (1280px)';
            }
        });
    });

    // ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeDrawer(); }
    });
}());
</script>
@endpush
