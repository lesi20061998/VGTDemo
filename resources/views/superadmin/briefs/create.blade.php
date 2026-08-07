@extends('superadmin.layouts.app')
@section('title', 'Tạo Brief mới | Super Admin')
@section('page-title', 'Tạo Brief mới')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.briefs.index') }}" class="text-gray-500 hover:text-[#001B4E] inline-flex items-center transition-colors">
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

    <form action="{{ route('superadmin.briefs.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Thông tin Yêu cầu (Brief)
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-form.label value="Tên Brief (Dự án tương lai)" required="true" />
                                <x-form.input name="title" :value="old('title')" required="true" placeholder="VD: Thiết kế Website Bán Hàng..." />
                            </div>
                            <div>
                                <x-form.label value="Tên Khách hàng" required="true" />
                                <x-form.input name="client_name" :value="old('client_name')" required="true" placeholder="VD: Công ty TNHH ABC" />
                            </div>
                        </div>
                        
                        <div>
                            <x-form.label value="Yêu cầu chi tiết" required="true" />
                            <x-form.rich-editor name="requirements" :value="old('requirements')" placeholder="Nhập mô tả các yêu cầu từ phía khách hàng..." />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ngân sách & Thời gian
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Ngân sách dự kiến (VNĐ)" />
                            <x-form.input name="budget" type="number" :value="old('budget')" placeholder="VD: 50000000" />
                        </div>
                        
                        <div>
                            <x-form.label value="Thời hạn (Deadline)" />
                            <x-form.input type="date" name="deadline" :value="old('deadline')" />
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <x-form.button type="submit" class="w-full justify-center flex text-lg py-3">
                        Lưu Brief Mới
                    </x-form.button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection