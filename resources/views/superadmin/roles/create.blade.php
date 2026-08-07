@extends('superadmin.layouts.app')
@section('title', 'Thêm Vai trò mới | Super Admin')
@section('page-title', 'Thêm Vai trò mới')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.roles.index') }}" class="text-gray-500 hover:text-[#001B4E] inline-flex items-center transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại danh sách
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">Vui lòng kiểm tra lại thông tin bên dưới.</span>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.roles.store') }}" method="POST" id="create-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sidebar Info (1 col) -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        Thông tin Vai trò
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Mã Role (Ví dụ: editor)" required="true" />
                            <x-form.input name="name" :value="old('name')" required="true" placeholder="Bắt buộc viết thường, không dấu" />
                        </div>
                        <div>
                            <x-form.label value="Tên hiển thị (Ví dụ: Biên tập viên)" required="true" />
                            <x-form.input name="display_name" :value="old('display_name')" required="true" />
                        </div>
                        <div>
                            <x-form.label value="Mô tả chi tiết" />
                            <x-form.textarea name="description" :value="old('description')" rows="3" />
                        </div>
                    </div>
                </div>
                
                <div class="pt-2">
                    <x-form.button type="submit" form="create-form" class="w-full justify-center flex text-lg py-3 shadow-sm">
                        Lưu Role
                    </x-form.button>
                </div>
            </div>
            
            <!-- Main Content Permissions (2 cols) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Cấp quyền (Permissions)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($permissions as $group => $perms)
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <h4 class="font-bold text-gray-700 mb-3 capitalize text-sm uppercase tracking-wider">{{ $group ?: 'Chung' }}</h4>
                            <div class="space-y-3">
                                @foreach($perms as $perm)
                                <label class="flex items-start group cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" {{ (is_array(old('permissions')) && in_array($perm->id, old('permissions'))) ? 'selected' : '' }} class="mt-1 rounded text-[#001B4E] focus:ring-[#001B4E] border-gray-300">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-[#001B4E] transition-colors">
                                        <span class="font-medium block">{{ $perm->display_name }}</span>
                                        <span class="text-xs text-gray-500">{{ $perm->description }}</span>
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection