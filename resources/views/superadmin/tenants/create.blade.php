@extends('superadmin.layouts.app')
@section('title', 'Tạo Tenant Mới | Super Admin')
@section('page-title', 'Multi-Tenant Management')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.tenants.index') }}" class="text-gray-500 hover:text-[#001B4E] inline-flex items-center transition-colors">
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

    <form method="POST" action="{{ route('superadmin.tenants.store') }}" id="create-form">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        Thông tin Website (Tenant)
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Tên Website" required="true" />
                            <x-form.input name="name" :value="old('name')" required="true" placeholder="VD: Cửa hàng ABC" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-form.label value="Mã Code" required="true" />
                                <x-form.input name="code" :value="old('code')" required="true" placeholder="VD: abc-store" />
                                <p class="text-[11px] text-gray-500 mt-1 italic">Chỉ dùng chữ thường, số và dấu gạch ngang</p>
                            </div>
                            
                            <div>
                                <x-form.label value="Domain" required="true" />
                                <x-form.input name="domain" :value="old('domain')" required="true" placeholder="VD: abc-store.local" />
                                <p class="text-[11px] text-gray-500 mt-1 italic">Domain hoặc subdomain cho website này</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Options Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Tùy chọn Khởi tạo
                    </h3>
                    
                    <div class="space-y-4">
                        <label class="flex items-start group cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="create_website" id="create_website" value="1" 
                                       {{ old('create_website') ? 'checked' : '' }}
                                       class="w-4 h-4 text-[#001B4E] bg-gray-100 border-gray-300 rounded focus:ring-[#001B4E]">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-gray-900">Tự động tạo website với database riêng và export source code</span>
                                <p class="text-gray-500 text-xs mt-1">Hệ thống sẽ tự động cấu hình và tạo ra một bản sao (clone) của mã nguồn</p>
                            </div>
                        </label>
                        
                        <div id="export_options" class="ml-7 {{ old('create_website') ? '' : 'hidden' }}">
                            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                                <x-form.label value="Đường dẫn export (tùy chọn)" />
                                <x-form.input name="export_path" :value="old('export_path')" placeholder="VD: c:\xampp\htdocs\ten-website" />
                                <p class="text-xs text-blue-600 mt-1">Để trống sẽ tự động tạo thư mục theo tên Code</p>
                            </div>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        Hệ thống
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Database" required="true" />
                            <x-form.input name="database_name" :value="old('database_name', 'agency_cms')" required="true" />
                            <p class="text-[11px] text-gray-500 mt-1 italic">Tên database (hiện tại mặc định dùng chung agency_cms)</p>
                        </div>
                        
                        <div>
                            <x-form.label value="Trạng thái" />
                            <x-form.select name="status" :options="['active' => 'Active (Hoạt động)', 'inactive' => 'Inactive (Tạm khóa)']" :value="old('status', 'active')" />
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <x-form.button type="submit" form="create-form" class="w-full justify-center flex text-lg py-3 shadow-sm">
                        Khởi tạo Tenant
                    </x-form.button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const createWebsiteCheckbox = document.getElementById('create_website');
    const exportOptions = document.getElementById('export_options');
    
    if (createWebsiteCheckbox) {
        createWebsiteCheckbox.addEventListener('change', function() {
            if (this.checked) {
                exportOptions.classList.remove('hidden');
                // Optional animation
                exportOptions.classList.add('animate-fade-in-down');
            } else {
                exportOptions.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection
