@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Sửa ' . ($config['name'] ?? 'dữ liệu'))
@section('page-title', 'Sửa ' . ($config['name'] ?? 'dữ liệu'))

@section('content')
@php
    $actionUrl = isset($currentProject) 
        ? route('project.admin.posts.update', ['projectCode' => $currentProject->code, 'post' => $post]) 
        : route('superadmin.posts.update', ['post' => $post]);
        
    $indexUrl = isset($currentProject) 
        ? route('project.admin.posts.index', ['projectCode' => $currentProject->code, 'type' => $postType]) 
        : route('superadmin.posts.index', ['type' => $postType]);
@endphp

<form method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data" x-data="postForm()">
    @csrf
    @method('PUT')
    
    <input type="hidden" name="post_type" value="{{ $postType }}">
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Sửa {{ $config['name'] ?? 'nội dung' }}: {{ $post->title }}</h1>
                <p class="text-sm text-gray-500">Cập nhật thông tin {{ $config['name'] ?? 'nội dung' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ $indexUrl }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Cập nhật
                </button>
            </div>
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Form chính -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nội dung chính</h2>
                
                @if(in_array('title', $config['supports'] ?? []))
                <!-- Tiêu đề -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tiêu đề *
                    </label>
                    <input type="text" name="translations[{{ $currentLang ?? 'vi' }}][title]" 
                           value="{{ old('translations.'.($currentLang ?? 'vi').'.title', $post->getTranslation('title', $currentLang ?? 'vi', false)) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                @endif

                <!-- Slug (Đường dẫn tĩnh) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (Đường dẫn tĩnh)</label>
                    <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nhập slug đường dẫn...">
                    <p class="text-xs text-gray-500 mt-1">Đường dẫn slug cho bài viết (chỉnh sửa độc lập, không tự động thay đổi theo tiêu đề).</p>
                </div>

                @if(in_array('excerpt', $config['supports'] ?? []))
                <!-- Tóm tắt -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt</label>
                    <textarea name="translations[{{ $currentLang ?? 'vi' }}][excerpt]" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('translations.'.($currentLang ?? 'vi').'.excerpt', $post->getTranslation('excerpt', $currentLang ?? 'vi', false)) }}</textarea>
                </div>
                @endif

                @if(in_array('content', $config['supports'] ?? []))
                <!-- Nội dung -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nội dung chi tiết
                    </label>
                    <div class="summernote-container">
                        <textarea name="translations[{{ $currentLang ?? 'vi' }}][content]" class="tinymce-editor">{{ old('translations.'.($currentLang ?? 'vi').'.content', $post->getTranslation('content', $currentLang ?? 'vi', false)) }}</textarea>
                    </div>
                </div>
                @endif
            </div>

            <!-- Dynamic Meta Fields -->
            @if(isset($config['fields']) && count($config['fields']) > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Dữ liệu mở rộng</h2>
                @foreach($config['fields'] as $key => $field)
                    @php
                        $metaValue = isset($post->meta_data[$key]) ? $post->meta_data[$key] : ($field['default'] ?? '');
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach($field['options'] as $optVal => $optLabel)
                                    <option value="{{ $optVal }}" {{ old('meta_data.'.$key, $metaValue) == $optVal ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'user_select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn nhân sự...</option>
                                <!-- Populate users dynamically later -->
                                <option value="1" {{ old('meta_data.'.$key, $metaValue) == '1' ? 'selected' : '' }}>Admin</option>
                            </select>
                        @else
                            <input type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}" 
                                   name="meta_data[{{ $key }}]" 
                                   value="{{ old('meta_data.'.$key, $metaValue) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
            @if($post->post_type === 'property')
            <!-- Thư viện ảnh 360 cho Bất động sản -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Thư viện ảnh 360
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Các liên kết ảnh 360 (Mỗi link một dòng)</label>
                        <textarea name="meta_data[gallery_360]" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg">{{ old('meta_data.gallery_360', $post->meta_data['gallery_360'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            @endif

            <!-- SEO Analyzer -->
            @if(in_array('seo', $config['supports'] ?? []))
                @include('cms.components.seo-analyzer', ['contentType' => $config['name'] ?? 'nội dung', 'model' => $post])
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
                            <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Xuất bản</option>
                            <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đặt lịch đăng</label>
                        <input type="datetime-local" name="published_at" 
                               value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}" 
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
                    @include('cms.components.media-picker', ['name' => 'featured_image', 'value' => old('featured_image', $post->featured_image)])
                </div>
            </div>
            @endif

            @if(in_array('template', $config['supports'] ?? []))
            <!-- Template cho trang -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Template</h2>
                <select name="template" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Mặc định</option>
                    <option value="contact" {{ old('template', $post->template) == 'contact' ? 'selected' : '' }}>Liên hệ</option>
                    <option value="about" {{ old('template', $post->template) == 'about' ? 'selected' : '' }}>Giới thiệu</option>
                    <option value="landing" {{ old('template', $post->template) == 'landing' ? 'selected' : '' }}>Landing Page</option>
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
