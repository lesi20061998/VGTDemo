{{-- MODIFIED: 2025-11-22 --}}
@extends('cms.layouts.app')

@section('title', 'Thêm danh mục')
@section('page-title', 'Thêm danh mục mới')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ route('cms.categories.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg @error('name') border-red-500 @enderror">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục cha</label>
            <select name="parent_id" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Danh mục gốc (Cấp 1)</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                        {{ str_repeat('—', $parent->level) }} {{ $parent->name }} (Cấp {{ $parent->level + 1 }})
                    </option>
                @endforeach
            </select>
            @error('parent_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh danh mục</label>
            @include('cms.components.file-manager', ['field' => 'image', 'label' => 'Ảnh danh mục'])
        </div>

        <div class="mt-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4">
                <span class="ml-2 text-sm text-gray-700">Kích hoạt</span>
            </label>
        </div>

        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('cms.categories.index') }}" class="px-4 py-2 text-gray-700 border rounded-lg">Hủy</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Thêm danh mục</button>
        </div>
    </form>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
