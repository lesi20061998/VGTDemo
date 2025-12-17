@extends('cms.layouts.app')

@section('title', 'Mục lục tự động')
@section('page-title', 'Table of Contents - Mục lục bài viết')

@section('content')
@include('cms.settings.partials.back-link')

@php
    $projectCode = request()->route('projectCode') ?? request()->segment(1);
    $settingsSaveUrl = $projectCode ? route('project.admin.settings.save', ['projectCode' => $projectCode]) : url('/admin/settings/save');
@endphp

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ $settingsSaveUrl }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @php
                $toc = setting('toc', []);
                $enabled = $toc['enabled'] ?? true;
                $position = $toc['position'] ?? 'before_content';
                $title = $toc['title'] ?? 'Mục lục';
                $minHeadings = $toc['min_headings'] ?? 3;
                $headingLevels = $toc['heading_levels'] ?? ['h2', 'h3'];
                $showNumbers = $toc['show_numbers'] ?? true;
                $collapsible = $toc['collapsible'] ?? true;
                $smoothScroll = $toc['smooth_scroll'] ?? true;
                $highlightActive = $toc['highlight_active'] ?? true;
                $stickyToc = $toc['sticky_toc'] ?? false;
            @endphp

            <!-- Enable TOC -->
            <div class="border-b pb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="toc[enabled]" value="1" {{ $enabled ? 'checked' : '' }} class="mr-2 rounded">
                    <span class="font-medium text-lg">Bật mục lục tự động</span>
                </label>
                <p class="text-sm text-gray-500 mt-1 ml-6">Tự động tạo mục lục từ các thẻ heading trong bài viết</p>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium mb-2">Tiêu đề mục lục</label>
                <input type="text" name="toc[title]" value="{{ $title }}" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Tiêu đề hiển thị ở đầu mục lục</p>
            </div>

            <!-- Position -->
            <div>
                <label class="block text-sm font-medium mb-2">Vị trí hiển thị</label>
                <select name="toc[position]" class="w-full px-4 py-2 border rounded-lg">
                    <option value="before_content" {{ $position == 'before_content' ? 'selected' : '' }}>Trước nội dung</option>
                    <option value="after_title" {{ $position == 'after_title' ? 'selected' : '' }}>Sau tiêu đề bài viết</option>
                    <option value="sidebar" {{ $position == 'sidebar' ? 'selected' : '' }}>Sidebar (nếu có)</option>
                    <option value="manual" {{ $position == 'manual' ? 'selected' : '' }}>Thủ công (dùng shortcode)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Shortcode: <code class="bg-gray-100 px-2 py-1 rounded">[toc]</code></p>
            </div>

            <!-- Min Headings -->
            <div>
                <label class="block text-sm font-medium mb-2">Số heading tối thiểu</label>
                <input type="number" name="toc[min_headings]" value="{{ $minHeadings }}" min="1" max="10" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Chỉ hiển thị mục lục khi bài viết có ít nhất số heading này</p>
            </div>

            <!-- Heading Levels -->
            <div>
                <label class="block text-sm font-medium mb-3">Cấp độ heading</label>
                <div class="space-y-2">
                    @foreach(['h2' => 'H2 - Tiêu đề cấp 2', 'h3' => 'H3 - Tiêu đề cấp 3', 'h4' => 'H4 - Tiêu đề cấp 4', 'h5' => 'H5 - Tiêu đề cấp 5', 'h6' => 'H6 - Tiêu đề cấp 6'] as $level => $label)
                    <label class="flex items-center">
                        <input type="checkbox" name="toc[heading_levels][]" value="{{ $level }}" 
                               {{ in_array($level, $headingLevels) ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2">Chọn các cấp độ heading sẽ xuất hiện trong mục lục</p>
            </div>

            <!-- Display Options -->
            <div class="border-t pt-4">
                <h3 class="font-medium mb-3">Tùy chọn hiển thị</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="toc[show_numbers]" value="1" {{ $showNumbers ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Hiển thị số thứ tự (1, 2, 3...)</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="toc[collapsible]" value="1" {{ $collapsible ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Cho phép thu gọn/mở rộng</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="toc[smooth_scroll]" value="1" {{ $smoothScroll ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Cuộn mượt khi click vào mục lục</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="toc[highlight_active]" value="1" {{ $highlightActive ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Highlight mục đang xem</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="toc[sticky_toc]" value="1" {{ $stickyToc ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Mục lục dính (sticky) khi cuộn trang</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>

<!-- Preview Demo -->
<div class="bg-gray-50 rounded-lg p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4">Xem trước (Demo)</h3>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- TOC Preview -->
        <div class="lg:col-span-1">
            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 sticky top-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">{{ $title }}</h4>
                    <button type="button" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
                <nav class="space-y-1 text-sm">
                    <a href="#" class="block py-1.5 px-3 text-blue-600 bg-blue-50 rounded hover:bg-blue-100">
                        {{ $showNumbers ? '1. ' : '' }}Giới thiệu
                    </a>
                    <a href="#" class="block py-1.5 px-3 text-gray-700 hover:bg-gray-100 rounded">
                        {{ $showNumbers ? '2. ' : '' }}Tính năng chính
                    </a>
                    @if(in_array('h3', $headingLevels))
                    <a href="#" class="block py-1.5 px-3 pl-6 text-gray-600 hover:bg-gray-100 rounded text-xs">
                        {{ $showNumbers ? '2.1. ' : '' }}Tính năng A
                    </a>
                    <a href="#" class="block py-1.5 px-3 pl-6 text-gray-600 hover:bg-gray-100 rounded text-xs">
                        {{ $showNumbers ? '2.2. ' : '' }}Tính năng B
                    </a>
                    @endif
                    <a href="#" class="block py-1.5 px-3 text-gray-700 hover:bg-gray-100 rounded">
                        {{ $showNumbers ? '3. ' : '' }}Hướng dẫn sử dụng
                    </a>
                    <a href="#" class="block py-1.5 px-3 text-gray-700 hover:bg-gray-100 rounded">
                        {{ $showNumbers ? '4. ' : '' }}Kết luận
                    </a>
                </nav>
            </div>
        </div>

        <!-- Content Preview -->
        <div class="lg:col-span-2">
            <div class="bg-white border rounded-lg p-6 prose max-w-none">
                <h1 class="text-2xl font-bold mb-4">Tiêu đề bài viết</h1>
                <h2 class="text-xl font-semibold mt-6 mb-3">Giới thiệu</h2>
                <p class="text-gray-600 mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                
                <h2 class="text-xl font-semibold mt-6 mb-3">Tính năng chính</h2>
                <p class="text-gray-600 mb-4">Sed do eiusmod tempor incididunt ut labore et dolore...</p>
                
                @if(in_array('h3', $headingLevels))
                <h3 class="text-lg font-semibold mt-4 mb-2">Tính năng A</h3>
                <p class="text-gray-600 mb-3">Ut enim ad minim veniam, quis nostrud exercitation...</p>
                
                <h3 class="text-lg font-semibold mt-4 mb-2">Tính năng B</h3>
                <p class="text-gray-600 mb-3">Duis aute irure dolor in reprehenderit in voluptate...</p>
                @endif
                
                <h2 class="text-xl font-semibold mt-6 mb-3">Hướng dẫn sử dụng</h2>
                <p class="text-gray-600 mb-4">Excepteur sint occaecat cupidatat non proident...</p>
                
                <h2 class="text-xl font-semibold mt-6 mb-3">Kết luận</h2>
                <p class="text-gray-600 mb-4">Sunt in culpa qui officia deserunt mollit anim...</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
    <p class="text-sm text-blue-800">
        <strong>Lưu ý:</strong> Mục lục sẽ tự động tạo anchor links cho các heading. 
        Hệ thống sẽ tự động thêm ID cho các thẻ heading nếu chưa có.
    </p>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h3 class="font-semibold mb-3">Code mẫu Frontend</h3>
    <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto">
        <pre class="text-sm"><code>&lt;!-- Thêm vào template bài viết --&gt;
@if(setting('toc.enabled') && $post->headings_count >= setting('toc.min_headings', 3))
    &lt;div class="toc-container"&gt;
        &lt;h3&gt;{{ setting('toc.title', 'Mục lục') }}&lt;/h3&gt;
        &lt;nav&gt;
            @foreach($post->toc as $item)
                &lt;a href="#{{ $item['id'] }}"&gt;{{ $item['text'] }}&lt;/a&gt;
            @endforeach
        &lt;/nav&gt;
    &lt;/div&gt;
@endif</code></pre>
    </div>
</div>

@endsection
