@extends('superadmin.layouts.app')
@section('title', 'Cập nhật Task | Super Admin')
@section('page-title', 'Cập nhật Task: ' . $task->title)

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.tasks.index') }}" class="text-gray-500 hover:text-[#001B4E] inline-flex items-center transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại bảng Tasks
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

    <form action="{{ route('superadmin.tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Thông tin Task
                        </h3>
                        <span class="text-xs font-semibold text-gray-400">ID: #{{ $task->id }}</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Tiêu đề Task" required="true" />
                            <x-form.input name="title" :value="old('title', $task->title)" required="true" />
                        </div>
                        
                        <div>
                            <x-form.label value="Mô tả chi tiết công việc" required="true" />
                            <x-form.rich-editor name="description" :value="old('description', $task->description)" />
                        </div>
                    </div>
                </div>
                
                <!-- Review Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Kiểm duyệt Kết quả (Dành cho Account)
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kết quả Dev gửi:</label>
                            <div class="p-3 bg-white border border-gray-200 rounded text-sm text-gray-700 whitespace-pre-wrap min-h-[100px]">{{ $task->result_notes ?: 'Chưa có kết quả' }}</div>
                        </div>
                        
                        <div>
                            <x-form.label value="Đánh giá / Trạng thái" />
                            <x-form.select name="status" :options="[
                                'todo' => 'Đưa lại về Cần làm', 
                                'in_progress' => 'Đang làm (Dev đang làm)', 
                                'review' => 'Chờ duyệt (Dev đã nộp)', 
                                'rework' => 'Làm lại (Không đạt)', 
                                'done' => 'Hoàn thành (Đạt)'
                            ]" :value="old('status', $task->status)" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Phân công & Thời gian
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Dự án" required="true" />
                            <x-form.select name="project_id" required="true">
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        
                        <div>
                            <x-form.label value="Giao cho (Dev)" required="true" />
                            <x-form.select name="dev_id" required="true">
                                @foreach($devs as $dev)
                                    <option value="{{ $dev->id }}" {{ old('dev_id', $task->dev_id) == $dev->id ? 'selected' : '' }}>{{ $dev->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        
                        <div class="pt-2 border-t border-gray-100">
                            <x-form.label value="Thời hạn (Deadline)" />
                            <x-form.input type="date" name="deadline" :value="old('deadline', $task->deadline?->format('Y-m-d'))" />
                        </div>
                        
                        <div class="pt-2 border-t border-gray-100 text-sm text-gray-500">
                            <p><strong>Ngày tạo:</strong> {{ $task->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-1"><strong>Cập nhật:</strong> {{ $task->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <x-form.button type="submit" class="w-full justify-center flex text-lg py-3">
                        Lưu Thay Đổi
                    </x-form.button>
                </div>
    </form>
                
                <!-- Delete Action -->
                <div class="mt-8 pt-6 border-t border-red-100">
                    <form action="{{ route('superadmin.tasks.destroy', $task->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 px-4 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 hover:border-red-300 font-medium transition-colors flex items-center justify-center" onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn Task này? Hành động này không thể hoàn tác.')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Xóa Task này
                        </button>
                    </form>
                </div>
            </div>
        </div>
</div>
@endsection