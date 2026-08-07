@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Thêm ' . ($config['name'] ?? 'dữ liệu') . ' mới')
@section('page-title', 'Tạo ' . ($config['name'] ?? 'dữ liệu'))

@section('content')
@php
    $actionUrl = isset($currentProject) 
        ? route('project.admin.posts.store', $currentProject->code) 
        : route('superadmin.posts.store');
    
    $indexUrl = isset($currentProject) 
        ? route('project.admin.posts.index', ['projectCode' => $currentProject->code, 'type' => $postType]) 
        : route('superadmin.posts.index', ['type' => $postType]);
@endphp

<form method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data" x-data="postForm()">
    @csrf
    <input type="hidden" name="post_type" value="{{ $postType }}">
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Tạo {{ $config['name'] ?? 'nội dung' }} mới</h1>
                <p class="text-sm text-gray-500">Nhập thông tin {{ $config['name'] ?? 'nội dung' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ $indexUrl }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Lưu {{ $config['name'] ?? 'dữ liệu' }}
                </button>
            </div>
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Form chính -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" x-model="slug" value="{{ old('slug') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Để trống để tự động tạo từ tiêu đề</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nội dung chính</h2>
                
                @if(in_array('title', $config['supports'] ?? []))
                <!-- Tiêu đề -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tiêu đề *
                    </label>
                    <input type="text" name="translations[{{ $currentLang ?? 'vi' }}][title]" value="{{ old('translations.'.($currentLang ?? 'vi').'.title') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                @endif

                @if(in_array('excerpt', $config['supports'] ?? []))
                <!-- Tóm tắt -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt</label>
                    <textarea name="translations[{{ $currentLang ?? 'vi' }}][excerpt]" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('translations.'.($currentLang ?? 'vi').'.excerpt') }}</textarea>
                </div>
                @endif

                @if(in_array('content', $config['supports'] ?? []))
                <!-- Nội dung -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nội dung chi tiết
                    </label>
                    <div class="summernote-container">
                        <textarea name="translations[{{ $currentLang ?? 'vi' }}][content]" class="tinymce-editor">{{ old('translations.'.($currentLang ?? 'vi').'.content') }}</textarea>
                    </div>
                </div>
                @endif
            </div>

            <!-- Dynamic Meta Fields -->
            @if(isset($config['fields']) && count($config['fields']) > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Dữ liệu mở rộng</h2>
                @foreach($config['fields'] as $key => $field)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach($field['options'] as $optVal => $optLabel)
                                    <option value="{{ $optVal }}" {{ old('meta_data.'.$key, $field['default'] ?? '') == $optVal ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'user_select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn nhân sự...</option>
                                <!-- Populate users dynamically later -->
                                <option value="1">Admin</option>
                            </select>
                        @else
                            <input type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}" 
                                   name="meta_data[{{ $key }}]" 
                                   value="{{ old('meta_data.'.$key, $field['default'] ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
            @if(($type ?? $postType ?? '') === 'property')
            <!-- Thư viện ảnh 360 cho Bất động sản -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Thư viện ảnh 360
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Các liên kết ảnh 360 (Mỗi link một dòng)</label>
                        <textarea name="meta_data[gallery_360]" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg">{{ old('meta_data.gallery_360') }}</textarea>
                    </div>
                </div>
            </div>
            @endif

            <!-- SEO Analyzer -->
            @if(in_array('seo', $config['supports'] ?? []))
                @include('cms.components.seo-analyzer', ['contentType' => $config['name'] ?? 'nội dung'])
            @endif
        </div>

        <!-- Cột phải: Sidebar -->
        <div class="space-y-6">
            <!-- Đặt lịch đăng -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Xuất bản</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Xuất bản</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đặt lịch đăng</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Để trống để đăng ngay</p>
                    </div>
                </div>
            </div>

            @if(in_array('featured_image', $config['supports'] ?? []))
            <!-- Ảnh đại diện -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Ảnh đại diện</h2>
                
                <div class="space-y-3">
                    @include('cms.components.media-picker', ['name' => 'featured_image', 'value' => old('featured_image')])
                </div>
            </div>
            @endif

            @if(in_array('template', $config['supports'] ?? []))
            <!-- Template cho trang -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Template</h2>
                <select name="template" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Mặc định</option>
                    <option value="contact">Liên hệ</option>
                    <option value="about">Giới thiệu</option>
                    <option value="landing">Landing Page</option>
                </select>
            </div>
            @endif
        </div>
    </div>
</form>

<script>
function postForm() {
    return {
        slug: ''
    }
}
</script>
@endsection
