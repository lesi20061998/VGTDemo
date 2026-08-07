@extends('superadmin.layouts.app')
@section('title', 'Cập nhật Feature Pack | Super Admin')
@section('page-title', 'Cập nhật Gói Tính Năng')

@section('content')
<div class="px-6 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-6 flex items-center">
        <a href="{{ route('superadmin.feature-packs.index') }}" class="text-gray-500 hover:text-[#001B4E] mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Cập nhật Feature Pack</h1>
            <p class="text-gray-500 mt-1">Chỉnh sửa thông tin gói tính năng: {{ $featurePack->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('superadmin.feature-packs.update', $featurePack->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Tính Năng <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $featurePack->name) }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 shadow-sm" 
                           placeholder="Ví dụ: E-commerce, Booking...">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã (Code) <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $featurePack->code) }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 shadow-sm font-mono text-sm" 
                           placeholder="Ví dụ: ecommerce, booking">
                    <p class="mt-1 text-xs text-gray-500">Mã này sẽ dùng để code xử lý logic khi tạo website, phải là duy nhất và không dấu, viết liền.</p>
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Group Name -->
                <div>
                    <label for="group_name" class="block text-sm font-medium text-gray-700 mb-1">Nhóm Tính Năng</label>
                    <input type="text" name="group_name" id="group_name" value="{{ old('group_name', $featurePack->group_name) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 shadow-sm" 
                           placeholder="Ví dụ: Sales, Marketing, Core...">
                    @error('group_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 shadow-sm" 
                              placeholder="Mô tả chi tiết về tính năng này...">{{ old('description', $featurePack->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $featurePack->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#001B4E] shadow-sm focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 h-5 w-5">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Kích hoạt (Tính năng này sẽ hiển thị để chọn khi tạo website)
                    </label>
                    @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-6 border-gray-100">
                <a href="{{ route('superadmin.feature-packs.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors shadow-sm">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium transition-colors shadow-sm">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
