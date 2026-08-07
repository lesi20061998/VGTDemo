@extends('superadmin.layouts.app')
@section('title', 'Cập nhật Brief | Super Admin')
@section('page-title', 'Cập nhật Brief: ' . $brief->title)

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('superadmin.briefs.update', $brief->id) }}" method="POST" id="edit-form">
                @csrf
                @method('PUT')
                
                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Thông tin Yêu cầu (Brief)
                        </h3>
                        <span class="text-xs font-semibold text-gray-400">ID: #{{ $brief->id }}</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-form.label value="Tên Brief" required="true" />
                                <x-form.input name="title" :value="old('title', $brief->title)" required="true" />
                            </div>
                            <div>
                                <x-form.label value="Tên Khách hàng" required="true" />
                                <x-form.input name="client_name" :value="old('client_name', $brief->client_name)" required="true" />
                            </div>
                        </div>
                        
                        <div>
                            <x-form.label value="Yêu cầu chi tiết" required="true" />
                            <x-form.rich-editor name="requirements" :value="old('requirements', $brief->requirements)" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Sidebar (1 col) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Config Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Ngân sách & Trạng thái
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <x-form.label value="Ngân sách dự kiến (VNĐ)" />
                        <x-form.input name="budget" type="number" form="edit-form" :value="old('budget', $brief->budget)" />
                    </div>
                    
                    <div>
                        <x-form.label value="Thời hạn (Deadline)" />
                        <x-form.input type="date" name="deadline" form="edit-form" :value="old('deadline', $brief->deadline?->format('Y-m-d'))" />
                    </div>
                    
                    <div class="pt-2 border-t border-gray-100">
                        <x-form.label value="Trạng thái" />
                        <x-form.select name="status" form="edit-form" :value="old('status', $brief->status)" :options="[
                            'draft' => 'Nháp (Draft)', 
                            'submitted' => 'Chờ duyệt (Submitted)', 
                            'approved' => 'Đã duyệt -> Chuyển thành Project', 
                            'rejected' => 'Từ chối (Rejected)'
                        ]" />
                        @if($brief->status != 'approved')
                        <p class="text-[11px] text-green-600 mt-1">* Chọn "Đã duyệt" để tự động tạo Project.</p>
                        @endif
                    </div>
                    
                    <div class="pt-2 border-t border-gray-100 text-sm text-gray-500">
                        <p><strong>Ngày tạo:</strong> {{ $brief->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mt-1"><strong>Cập nhật:</strong> {{ $brief->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="pt-2">
                <x-form.button type="submit" form="edit-form" class="w-full justify-center flex text-lg py-3 shadow-sm">
                    Lưu Thay Đổi
                </x-form.button>
            </div>
            
            <!-- Delete Action -->
            <div class="mt-8 pt-6 border-t border-red-100">
                <form action="{{ route('superadmin.briefs.destroy', $brief->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2 px-4 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 hover:border-red-300 font-medium transition-colors flex items-center justify-center" onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn Brief này?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Xóa Brief này
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection