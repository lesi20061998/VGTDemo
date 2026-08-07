@extends('superadmin.layouts.app')
@section('title', 'Tạo Task mới | Super Admin')
@section('page-title', 'Giao Task mới')

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

    <form action="{{ route('superadmin.tasks.store') }}" method="POST">
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
                        Thông tin Task
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Tiêu đề Task" required="true" />
                            <x-form.input name="title" :value="old('title')" required="true" placeholder="VD: Sửa lỗi giao diện..." />
                        </div>
                        
                        <div>
                            <x-form.label value="Mô tả chi tiết công việc" required="true" />
                            <x-form.rich-editor name="description" :value="old('description')" placeholder="Nhập chi tiết yêu cầu, link tham khảo, cách tái hiện lỗi..." />
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
                        Phân công & Phân loại
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Dự án" required="true" />
                            <x-form.select name="project_id" required="true">
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ (old('project_id') ?? $selectedProject) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        
                        <div>
                            <x-form.label value="Giao cho (Dev)" required="true" />
                            <x-form.select name="dev_id" required="true">
                                @foreach($devs as $dev)
                                    <option value="{{ $dev->id }}" {{ old('dev_id') == $dev->id ? 'selected' : '' }}>{{ $dev->name }} ({{ $dev->email }})</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        
                        <div class="pt-2 border-t border-gray-100">
                            <x-form.label value="Thời hạn (Deadline)" />
                            <x-form.input type="date" name="deadline" :value="old('deadline')" />
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <x-form.button type="submit" class="w-full justify-center flex text-lg py-3">
                        Lưu & Giao Task
                    </x-form.button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection