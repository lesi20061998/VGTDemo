{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa thuộc tính')
@section('page-title', 'Chỉnh sửa thuộc tính')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ route('project.admin.attributes.update', [request()->route('projectCode'), $attribute]) }}">
        @csrf @method('PUT')
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên thuộc tính *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $attribute->name) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                   required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $attribute->slug) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="mb-6">
            <label for="attribute_group_id" class="block text-sm font-medium text-gray-700 mb-2">Nhóm thuộc tính</label>
            <select id="attribute_group_id" name="attribute_group_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">-- Chọn nhóm --</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('attribute_group_id', $attribute->attribute_group_id) == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Loại *</label>
            <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                <option value="text" {{ old('type', $attribute->type) == 'text' ? 'selected' : '' }}>Text</option>
                <option value="number" {{ old('type', $attribute->type) == 'number' ? 'selected' : '' }}>Number</option>
                <option value="select" {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>Select</option>
                <option value="multiselect" {{ old('type', $attribute->type) == 'multiselect' ? 'selected' : '' }}>Multiselect</option>
                <option value="color" {{ old('type', $attribute->type) == 'color' ? 'selected' : '' }}>Color</option>
                <option value="boolean" {{ old('type', $attribute->type) == 'boolean' ? 'selected' : '' }}>Boolean</option>
            </select>
            @error('type')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_filterable" value="1" {{ old('is_filterable', $attribute->is_filterable) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Có thể lọc</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="is_required" value="1" {{ old('is_required', $attribute->is_required) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Bắt buộc</span>
            </label>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('project.admin.attributes.index', request()->route('projectCode')) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
