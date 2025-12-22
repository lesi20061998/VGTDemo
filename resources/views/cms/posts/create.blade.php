@extends('cms.layouts.app')

@section('title', $postType === 'page' ? 'Thêm trang mới' : 'Thêm bài viết mới')
@section('page-title', $postType === 'page' ? 'Tạo trang' : 'Tạo bài viết')

@section('content')
<form method="POST" action="{{ isset($currentProject) ? route('project.admin.posts.store', $currentProject->code) : '#' }}" enctype="multipart/form-data" x-data="postForm()">
    @csrf
    <input type="hidden" name="post_type" value="{{ $postType }}">
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $postType === 'page' ? 'Tạo trang mới' : 'Tạo bài viết mới' }}</h1>
                <p class="text-sm text-gray-500">Nhập thông tin {{ $postType === 'page' ? 'trang' : 'bài viết' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ isset($currentProject) ? route('project.admin.posts.index', $currentProject->code) : '#' }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Lưu {{ $postType === 'page' ? 'trang' : 'bài viết' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Language Switcher -->
    @include('cms.components.language-switcher')

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

            <!-- Nội dung bài viết -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nội dung {{ $postType === 'page' ? 'trang' : 'bài viết' }}</h2>
                
                <!-- Tiêu đề -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tiêu đề *
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Tóm tắt -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt</label>
                    <textarea name="excerpt" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('excerpt') }}</textarea>
                </div>

                <!-- Nội dung -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nội dung *
                    </label>
                    <div class="summernote-container">
                        <textarea name="content" class="summernote" required>{{ old('content') }}</textarea>
                    </div>
                    @error('content')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>


            </div>

            <!-- SEO Analyzer -->
            @include('cms.components.seo-analyzer', ['contentType' => $postType === 'page' ? 'trang' : 'bài viết'])
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

            <!-- Ảnh đại diện -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Ảnh đại diện</h2>
                
                <div class="space-y-3">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 aspect-video flex items-center justify-center">
                        <template x-if="featuredImage && featuredImage.length > 0">
                            <img :src="featuredImage" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!featuredImage || featuredImage.length === 0">
                            <div class="text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Chưa có ảnh</p>
                            </div>
                        </template>
                    </div>
                    
                    <input type="hidden" name="featured_image" x-model="featuredImage">
                    @include('cms.components.media-manager')
                </div>
            </div>

            @if($postType === 'page')
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
        slug: '',
        featuredImage: '',
        
        init() {
            window.addEventListener('media-selected', (e) => {
                const items = e.detail.items;
                this.featuredImage = items[0].url;
            });
        },
        
        generateSlug(title) {
            if (!this.slug) {
                this.slug = title.toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
            }
        }
    }
}
</script>
@endsection