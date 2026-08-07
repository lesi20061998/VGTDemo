@extends('superadmin.layouts.app')
@section('title', 'Thêm Quyền mới | Super Admin')
@section('page-title', 'Thêm Quyền mới')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.permissions.index') }}" class="text-gray-500 hover:text-[#001B4E] inline-flex items-center transition-colors">
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

    <form action="{{ route('superadmin.permissions.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Thông tin Quyền (Permission)
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-form.label value="Mã quyền (Không dấu)" required="true" />
                                <x-form.input name="name" :value="old('name')" required="true" placeholder="VD: manage-briefs, edit-users..." />
                            </div>
                            <div>
                                <x-form.label value="Tên hiển thị" required="true" />
                                <x-form.input name="display_name" :value="old('display_name')" required="true" placeholder="VD: Quản lý Brief" />
                            </div>
                        </div>
                        
                        <div>
                            <x-form.label value="Mô tả chi tiết" />
                            <x-form.textarea name="description" :value="old('description')" rows="3" placeholder="Mô tả rõ quyền này cho phép làm gì..." />
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar (1 col) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Config Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Phân nhóm
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Nhóm quyền" required="true" />
                            <x-form.input name="group" :value="old('group')" required="true" placeholder="VD: Agency, System, Content..." />
                            <p class="text-xs text-gray-500 mt-1">Dùng để gom nhóm khi hiển thị ở trang cấp quyền cho Role.</p>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <x-form.button type="submit" class="w-full justify-center flex text-lg py-3">
                        Lưu Quyền Mới
                    </x-form.button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection