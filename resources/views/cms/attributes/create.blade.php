{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Thêm thuộc tính')
@section('page-title', 'Thêm thuộc tính mới')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ route('project.admin.attributes.store', request()->route('projectCode')) }}">
        @csrf
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên thuộc tính *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                   required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="mb-6">
            <label for="attribute_group_id" class="block text-sm font-medium text-gray-700 mb-2">Nhóm thuộc tính</label>
            <select id="attribute_group_id" name="attribute_group_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">-- Chọn nhóm --</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('attribute_group_id') == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Loại *</label>
            <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
                <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Select</option>
                <option value="multiselect" {{ old('type') == 'multiselect' ? 'selected' : '' }}>Multiselect</option>
                <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Color</option>
                <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
            </select>
            @error('type')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_filterable" value="1" {{ old('is_filterable', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Có thể lọc</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Bắt buộc</span>
            </label>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('project.admin.attributes.index', request()->route('projectCode')) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Thêm thuộc tính
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.value) {
                // Vietnamese slug generation
                let slug = this.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // Remove accents
                    .replace(/đ/g, 'd').replace(/Đ/g, 'd') // Replace đ with d
                    .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-') // Replace multiple hyphens with single
                    .trim();
                slugInput.value = slug;
            }
        });
    }
});
</script>
@endsection
