@extends('cms.layouts.app')

@section('title', 'Mục lục tự động bài viết')
@section('page-title', 'Table of Contents - Mục lục bài viết')

@section('content')
@include('cms.settings.partials.back-link')

@php
    $projectCode = request()->route('projectCode') ?? request()->segment(1);
    $settingsSaveUrl = $projectCode ? route('project.admin.settings.save', ['projectCode' => $projectCode]) : url('/admin/settings/save');
    
    $toc = setting('toc', []);
    $enabled = isset($toc['enabled']) ? (bool)$toc['enabled'] : true;
    $position = $toc['position'] ?? 'before_content';
    $title = $toc['title'] ?? 'Mục lục bài viết';
    $minHeadings = (int)($toc['min_headings'] ?? 3);
    $headingLevels = $toc['heading_levels'] ?? ['h2', 'h3'];
    $style = $toc['style'] ?? 'style-1';
    $showNumbers = isset($toc['show_numbers']) ? (bool)$toc['show_numbers'] : true;
    $collapsible = isset($toc['collapsible']) ? (bool)$toc['collapsible'] : true;
    $smoothScroll = isset($toc['smooth_scroll']) ? (bool)$toc['smooth_scroll'] : true;
    $highlightActive = isset($toc['highlight_active']) ? (bool)$toc['highlight_active'] : true;
    $stickyToc = isset($toc['sticky_toc']) ? (bool)$toc['sticky_toc'] : false;
@endphp

<div x-data="{
    enabled: {{ $enabled ? 'true' : 'false' }},
    title: '{{ addslashes($title) }}',
    position: '{{ $position }}',
    minHeadings: {{ $minHeadings }},
    style: '{{ $style }}',
    showNumbers: {{ $showNumbers ? 'true' : 'false' }},
    collapsible: {{ $collapsible ? 'true' : 'false' }},
    smoothScroll: {{ $smoothScroll ? 'true' : 'false' }},
    highlightActive: {{ $highlightActive ? 'true' : 'false' }},
    stickyToc: {{ $stickyToc ? 'true' : 'false' }},
    headingLevels: {{ json_encode($headingLevels) }},
    activeTab: 'blade'
}" class="space-y-6">

    @if(session('alert'))
    <div class="p-4 rounded-lg {{ session('alert.type') === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' }}">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('alert.message') }}</span>
        </div>
    </div>
    @endif

    <form action="{{ $settingsSaveUrl }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Side: Configuration Form (7 Columns) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Main Status & Toggle Header Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Bật Mục Lục Bài Viết</h3>
                                <p class="text-xs text-gray-500">Tự động tạo danh mục từ các thẻ tiêu đề (Heading H2, H3...) trong bài viết</p>
                            </div>
                        </div>

                        <!-- Modern iOS Toggle Switch -->
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="toc[enabled]" value="0">
                            <input type="checkbox" name="toc[enabled]" value="1" x-model="enabled" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Basic Options Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5" x-show="enabled" x-transition>
                    <h3 class="text-base font-bold text-gray-900 border-b pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Cấu Hình Tiêu Đề & Vị Trí
                    </h3>

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tiêu đề mục lục</label>
                        <input type="text" name="toc[title]" x-model="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="VD: Mục lục bài viết">
                        <p class="text-xs text-gray-500 mt-1">Dòng chữ hiển thị làm tiêu đề ở đầu khung mục lục</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Position -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Vị trí chèn tự động</label>
                            <select name="toc[position]" x-model="position" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="before_content">Trước nội dung bài viết</option>
                                <option value="after_first_heading">Sau thẻ Heading đầu tiên</option>
                                <option value="manual">Thủ công (Dùng Shortcode [toc])</option>
                            </select>
                        </div>

                        <!-- Min Headings -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Số Heading tối thiểu</label>
                            <input type="number" name="toc[min_headings]" x-model="minHeadings" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Chỉ hiển thị khi bài viết có từ N heading trở lên</p>
                        </div>
                    </div>

                    <!-- Heading Levels -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cấp độ Heading thu thập</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['h2' => 'H2 (Cấp 2)', 'h3' => 'H3 (Cấp 3)', 'h4' => 'H4 (Cấp 4)', 'h5' => 'H5 (Cấp 5)'] as $level => $label)
                            <label class="inline-flex items-center px-3 py-2 border rounded-lg cursor-pointer transition text-sm"
                                   :class="headingLevels.includes('{{ $level }}') ? 'bg-blue-50 border-blue-300 text-blue-700 font-semibold' : 'bg-gray-50 border-gray-200 text-gray-600'">
                                <input type="checkbox" name="toc[heading_levels][]" value="{{ $level }}" 
                                       x-model="headingLevels" class="sr-only">
                                <span>{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- PRESET LAYOUT STYLES CARD (GIAO DIỆN MẪU CÓ SẴN) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4" x-show="enabled" x-transition>
                    <h3 class="text-base font-bold text-gray-900 border-b pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                        Chọn Giao Diện Mẫu (Preset Style)
                    </h3>
                    <input type="hidden" name="toc[style]" :value="style">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Style 1: Modern Card -->
                        <div @click="style = 'style-1'" 
                             class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                             :class="style === 'style-1' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-gray-800">1. Style Hiện Đại (Modern)</span>
                                <span x-show="style === 'style-1'" class="w-3 h-3 rounded-full bg-blue-600"></span>
                            </div>
                            <div class="bg-gray-50 border border-blue-200 rounded-lg p-2.5 text-xs text-gray-600">
                                <div class="font-semibold text-blue-700 border-b border-blue-100 pb-1 mb-1 flex justify-between">
                                    <span>Mục lục</span><span>▼</span>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-blue-600 font-medium">1. Giới thiệu</div>
                                    <div class="pl-2 text-gray-500">1.1 Lợi ích</div>
                                </div>
                            </div>
                        </div>

                        <!-- Style 2: Minimalist Clean -->
                        <div @click="style = 'style-2'" 
                             class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                             :class="style === 'style-2' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-gray-800">2. Style Tối Giản (Minimal)</span>
                                <span x-show="style === 'style-2'" class="w-3 h-3 rounded-full bg-blue-600"></span>
                            </div>
                            <div class="border-l-4 border-emerald-500 pl-3 py-1 bg-emerald-50/30 text-xs text-gray-600">
                                <div class="font-bold text-gray-800 mb-1">Mục lục bài viết</div>
                                <div class="text-emerald-700 font-medium">• 1. Giới thiệu</div>
                            </div>
                        </div>

                        <!-- Style 3: Classic Boxed -->
                        <div @click="style = 'style-3'" 
                             class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                             :class="style === 'style-3' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-gray-800">3. Style Cổ Điển (Classic)</span>
                                <span x-show="style === 'style-3'" class="w-3 h-3 rounded-full bg-blue-600"></span>
                            </div>
                            <div class="bg-amber-50/80 border border-amber-200 rounded p-2 text-xs text-gray-700">
                                <div class="font-bold text-amber-900 mb-1">Mục Lục</div>
                                <div>1. Giới thiệu tổng quan</div>
                            </div>
                        </div>

                        <!-- Style 4: Floating Card -->
                        <div @click="style = 'style-4'" 
                             class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                             :class="style === 'style-4' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-gray-800">4. Style Nổi 3D (Floating)</span>
                                <span x-show="style === 'style-4'" class="w-3 h-3 rounded-full bg-blue-600"></span>
                            </div>
                            <div class="bg-white border rounded-xl p-2.5 shadow-sm text-xs text-gray-700">
                                <div class="font-bold text-purple-700 mb-1 flex items-center justify-between">
                                    <span>Mục lục</span><span class="bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded text-[10px]">TOC</span>
                                </div>
                                <div class="text-purple-600 font-medium">1. Giới thiệu</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggles Options List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4" x-show="enabled" x-transition>
                    <h3 class="text-base font-bold text-gray-900 border-b pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Tính Năng Nâng Cao (Toggles)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Toggle 1: Show Numbers -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium text-sm text-gray-800">Hiển thị số thứ tự</span>
                                <p class="text-xs text-gray-500">Đánh số (1, 1.1, 1.2...)</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="toc[show_numbers]" value="0">
                                <input type="checkbox" name="toc[show_numbers]" value="1" x-model="showNumbers" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Toggle 2: Collapsible -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium text-sm text-gray-800">Cho phép Thu Gọn</span>
                                <p class="text-xs text-gray-500">Nút ẩn/hiện danh sách</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="toc[collapsible]" value="0">
                                <input type="checkbox" name="toc[collapsible]" value="1" x-model="collapsible" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Toggle 3: Smooth Scroll -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium text-sm text-gray-800">Cuộn mượt (Smooth Scroll)</span>
                                <p class="text-xs text-gray-500">Hiệu ứng lướt mượt đến mục</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="toc[smooth_scroll]" value="0">
                                <input type="checkbox" name="toc[smooth_scroll]" value="1" x-model="smoothScroll" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Toggle 4: Highlight Active -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium text-sm text-gray-800">Highlight mục đang xem</span>
                                <p class="text-xs text-gray-500">Đổi màu mục theo vị trí cuộn</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="toc[highlight_active]" value="0">
                                <input type="checkbox" name="toc[highlight_active]" value="1" x-model="highlightActive" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Toggle 5: Sticky TOC -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg md:col-span-2">
                            <div>
                                <span class="font-medium text-sm text-gray-800">Mục lục dính (Sticky)</span>
                                <p class="text-xs text-gray-500">Giữ cố định ở góc màn hình khi cuộn bài viết</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="toc[sticky_toc]" value="0">
                                <input type="checkbox" name="toc[sticky_toc]" value="1" x-model="stickyToc" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Lưu Cấu Hình Mục Lục</span>
                    </button>
                </div>

            </div>

            <!-- Right Side: Realtime Live Preview (5 Columns) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <div class="flex items-center justify-between border-b pb-3 mb-4">
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Xem Trước Trực Tiếp (Live Demo)
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              :class="enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                              x-text="enabled ? 'Đang bật' : 'Đang tắt'"></span>
                    </div>

                    <div x-show="!enabled" class="text-center py-10 bg-gray-50 rounded-xl border border-dashed text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <p class="text-sm font-medium">Mục lục tự động đang TẮT</p>
                    </div>

                    <div x-show="enabled">
                        
                        <!-- STYLE 1 PREVIEW: MODERN CARD -->
                        <div x-show="style === 'style-1'" class="bg-gradient-to-br from-blue-50/80 to-indigo-50/50 border border-blue-200/80 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-3 border-b border-blue-100 pb-2">
                                <h4 class="font-bold text-blue-900 text-base flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h7"/></svg>
                                    <span x-text="title"></span>
                                </h4>
                                <button type="button" x-show="collapsible" class="text-blue-500 hover:text-blue-700 text-xs font-semibold bg-white px-2 py-1 rounded-md border border-blue-200 shadow-xs">Thu gọn ▲</button>
                            </div>
                            <nav class="space-y-1.5 text-sm">
                                <a href="#" class="flex items-center gap-2 py-1.5 px-3 bg-white text-blue-700 font-semibold rounded-lg shadow-xs border border-blue-100">
                                    <span x-show="showNumbers" class="text-blue-500 text-xs">1.</span> Giới thiệu Đông Y 1
                                </a>
                                <a href="#" class="flex items-center gap-2 py-1.5 px-3 text-gray-700 hover:bg-white/80 rounded-lg">
                                    <span x-show="showNumbers" class="text-gray-400 text-xs">2.</span> Lợi ích của Thảo Dược
                                </a>
                                <div x-show="headingLevels.includes('h3')" class="pl-5 space-y-1">
                                    <a href="#" class="block py-1 text-xs text-gray-600 hover:text-blue-600">
                                        <span x-show="showNumbers">2.1.</span> Thanh nhiệt giải độc
                                    </a>
                                    <a href="#" class="block py-1 text-xs text-gray-600 hover:text-blue-600">
                                        <span x-show="showNumbers">2.2.</span> Bồi bổ khí huyết
                                    </a>
                                </div>
                                <a href="#" class="flex items-center gap-2 py-1.5 px-3 text-gray-700 hover:bg-white/80 rounded-lg">
                                    <span x-show="showNumbers" class="text-gray-400 text-xs">3.</span> Hướng dẫn sử dụng
                                </a>
                            </nav>
                        </div>

                        <!-- STYLE 2 PREVIEW: MINIMALIST CLEAN -->
                        <div x-show="style === 'style-2'" class="border-l-4 border-emerald-500 bg-emerald-50/30 pl-4 pr-3 py-4 rounded-r-xl">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-gray-900 text-base" x-text="title"></h4>
                                <span x-show="collapsible" class="text-xs text-emerald-600 cursor-pointer">[Ẩn]</span>
                            </div>
                            <nav class="space-y-1 text-sm">
                                <a href="#" class="block text-emerald-700 font-semibold py-1">
                                    <span x-show="showNumbers">1. </span>Giới thiệu Đông Y 1
                                </a>
                                <a href="#" class="block text-gray-700 hover:text-emerald-600 py-1">
                                    <span x-show="showNumbers">2. </span>Lợi ích của Thảo Dược
                                </a>
                                <div x-show="headingLevels.includes('h3')" class="pl-4 space-y-0.5 text-xs text-gray-500">
                                    <div><span x-show="showNumbers">2.1 </span>Thanh nhiệt giải độc</div>
                                    <div><span x-show="showNumbers">2.2 </span>Bồi bổ khí huyết</div>
                                </div>
                                <a href="#" class="block text-gray-700 hover:text-emerald-600 py-1">
                                    <span x-show="showNumbers">3. </span>Hướng dẫn sử dụng
                                </a>
                            </nav>
                        </div>

                        <!-- STYLE 3 PREVIEW: CLASSIC BOXED -->
                        <div x-show="style === 'style-3'" class="bg-amber-50/80 border border-amber-200/90 rounded-lg p-4">
                            <div class="flex items-center justify-between border-b border-amber-200/60 pb-2 mb-3">
                                <h4 class="font-serif font-bold text-amber-900 text-base" x-text="title"></h4>
                                <span x-show="collapsible" class="text-xs text-amber-700 cursor-pointer">[-]</span>
                            </div>
                            <nav class="space-y-1 text-sm font-serif">
                                <a href="#" class="block text-amber-900 font-bold hover:underline">
                                    <span x-show="showNumbers">I. </span>Giới thiệu Đông Y 1
                                </a>
                                <a href="#" class="block text-amber-800 hover:underline">
                                    <span x-show="showNumbers">II. </span>Lợi ích của Thảo Dược
                                </a>
                                <div x-show="headingLevels.includes('h3')" class="pl-4 text-xs text-amber-700 space-y-1">
                                    <div><span x-show="showNumbers">1. </span>Thanh nhiệt giải độc</div>
                                    <div><span x-show="showNumbers">2. </span>Bồi bổ khí huyết</div>
                                </div>
                                <a href="#" class="block text-amber-800 hover:underline">
                                    <span x-show="showNumbers">III. </span>Hướng dẫn sử dụng
                                </a>
                            </nav>
                        </div>

                        <!-- STYLE 4 PREVIEW: FLOATING SHADOW -->
                        <div x-show="style === 'style-4'" class="bg-white border rounded-2xl p-5 shadow-lg shadow-purple-50">
                            <div class="flex items-center justify-between mb-3 border-b pb-2">
                                <h4 class="font-bold text-purple-900 text-base" x-text="title"></h4>
                                <span class="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded-full font-semibold">TOC</span>
                            </div>
                            <nav class="space-y-1.5 text-sm">
                                <a href="#" class="block py-1.5 px-3 bg-purple-50 text-purple-700 font-semibold rounded-xl">
                                    <span x-show="showNumbers">1. </span>Giới thiệu Đông Y 1
                                </a>
                                <a href="#" class="block py-1.5 px-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                                    <span x-show="showNumbers">2. </span>Lợi ích của Thảo Dược
                                </a>
                                <a href="#" class="block py-1.5 px-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                                    <span x-show="showNumbers">3. </span>Hướng dẫn sử dụng
                                </a>
                            </nav>
                        </div>

                        <!-- Info Badge -->
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800 space-y-1">
                            <p class="font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Cách hoạt động ngoài Frontend:
                            </p>
                            <p>Hệ thống tự động phân tích bài viết, đánh số ID cho các thẻ Heading và sinh ra mục lục theo mẫu giao diện đã chọn.</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@endsection
