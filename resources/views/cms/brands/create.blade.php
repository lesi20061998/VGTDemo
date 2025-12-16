{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Thêm nhà sản xuất')
@section('page-title', 'Thêm nhà sản xuất mới')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ route('cms.brands.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên nhà sản xuất *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                   required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug (tự động tạo nếu để trống)</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror">
            @error('slug')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
            <x-summernote name="description" :value="old('description')" :height="300" />
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6 bg-white rounded-lg shadow-sm p-6 space-y-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Logo thương hiệu</label>
            @include('cms.components.file-manager', ['field' => 'logo', 'label' => 'Logo thương hiệu'])
            @error('logo')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Kích hoạt</span>
            </label>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('cms.brands.index') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Thêm nhà sản xuất
            </button>
        </div>
    </form>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.getElementById('name').addEventListener('input', function() {
    if (!document.getElementById('slug').value) {
        document.getElementById('slug').value = this.value.toLowerCase().replace(/\s+/g, '-');
    }
});
</script>
@endsection
