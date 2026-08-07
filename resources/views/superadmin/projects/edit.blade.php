@extends('superadmin.layouts.app')
@section('title', 'Sửa Dự án | Super Admin')
@section('page-title', 'Chỉnh sửa Dự án: ' . $project->name)

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.projects.index') }}" class="text-gray-500 hover:text-[#001B4E] inline-flex items-center transition-colors">
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

    <form method="POST" action="{{ route('superadmin.projects.update', $project) }}" id="edit-form">
        @csrf @method('PUT')
        
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
                            Thông tin Dự án
                        </h3>
                        <span class="text-xs font-semibold text-gray-400">Code: #{{ $project->code }}</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Tên Dự án" required="true" />
                            <x-form.input name="name" :value="old('name', $project->name)" required="true" />
                        </div>
                        
                        <div>
                            <x-form.label value="Subdomain" required="true" />
                            <x-form.input name="subdomain" :value="old('subdomain', $project->subdomain)" required="true" placeholder="localhost/project-code hoặc employee.domain.com/contract-code" />
                            <p class="text-[11px] text-gray-500 mt-1 italic">Ví dụ: localhost/HD01 (local) hoặc sivgt.domain.com/HD01</p>
                        </div>
                    </div>
                </div>
                
                <!-- Requirements Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                        Yêu cầu Kỹ thuật & Tính năng
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <x-form.label value="Yêu cầu Kỹ thuật" />
                            <x-form.rich-editor name="technical_requirements" :value="old('technical_requirements', $project->technical_requirements)" placeholder="Nhập yêu cầu kỹ thuật chi tiết..." />
                        </div>
                        
                        <div>
                            <x-form.label value="Các tính năng" />
                            <x-form.rich-editor name="features" :value="old('features', $project->features)" placeholder="Mô tả các tính năng..." />
                        </div>
                        
                        <div>
                            <x-form.label value="Feature Packs (Tính năng mở rộng)" />
                            <div class="mt-2 space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                @if($featurePacks->isNotEmpty())
                                    @php
                                        $selectedFeatures = is_array(old('cms_features')) ? old('cms_features') : (is_array($project->cms_features) ? $project->cms_features : []);
                                        $groupedPacks = $featurePacks->groupBy('group_name');
                                    @endphp
                                    @foreach($groupedPacks as $groupName => $packs)
                                        <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                            <h4 class="font-semibold text-gray-700 text-sm mb-2">{{ $groupName ?: 'Khác' }}</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                @foreach($packs as $pack)
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" name="cms_features[]" value="{{ $pack->code }}" class="rounded border-gray-300 text-[#001B4E] shadow-sm focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50" {{ in_array($pack->code, $selectedFeatures) ? 'checked' : '' }}>
                                                        <span class="ml-2 text-sm text-gray-600">{{ $pack->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <x-form.label value="Môi trường triển khai" />
                            <x-form.textarea name="environment" :value="old('environment', $project->environment)" rows="3" placeholder="Thông tin server, domain, database..." />
                        </div>
                        
                        <div>
                            <x-form.label value="Ghi chú khác" />
                            <x-form.textarea name="notes" :value="old('notes', $project->notes)" rows="3" placeholder="Các ghi chú bổ sung..." />
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
                        Phân công & Quản lý
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Nhân sự phụ trách (PM)" required="true" />
                            <select name="employee_id" required 
                                    class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 text-sm">
                                <option value="">-- Chọn Quản lý dự án --</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" 
                                    {{ old('employee_id', $project->admin_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} - {{ ucfirst($employee->position ?? 'staff') }}
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <x-form.error :message="$message" />
                            @enderror
                        </div>

                        <div>
                            <x-form.label value="Lập trình viên (Devs)" />
                            <select name="dev_ids[]" multiple 
                                    class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 text-sm" size="5">
                                @foreach($devs as $dev)
                                <option value="{{ $dev->id }}" 
                                    {{ in_array($dev->id, old('dev_ids', array_diff($project->employee_ids ?? [], [$project->admin_id]))) ? 'selected' : '' }}>
                                    {{ $dev->name }} - {{ ucfirst($dev->position ?? 'dev') }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-500 mt-1">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều</p>
                            @error('dev_ids')
                                <x-form.error :message="$message" />
                            @enderror
                        </div>
                        
                        <div>
                            <x-form.label value="Trạng thái" />
                            <x-form.select name="status" :value="old('status', $project->status)" :options="[
                                'pending' => 'Pending (Đang chờ)', 
                                'active' => 'Active (Hoạt động)', 
                                'assigned' => 'Assigned (Đã phân Dev)', 
                                'in_progress' => 'In Progress (Đang làm)', 
                                'on_hold' => 'On Hold (Tạm ngưng)', 
                                'error' => 'Error (Có lỗi)',
                                'completed' => 'Completed (Hoàn thành)'
                            ]" />
                        </div>
                        
                        <div class="pt-2 border-t border-gray-100">
                            <x-form.label value="Giá trị Dự án (VNĐ)" />
                            <x-form.input name="contract_value" :value="old('contract_value', $project->contract_value)" type="number" step="0.01" />
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                            <div>
                                <x-form.label value="Ngày Bắt đầu" />
                                <x-form.input name="start_date" :value="old('start_date', $project->start_date?->format('Y-m-d'))" type="date" />
                            </div>
                            <div>
                                <x-form.label value="Deadline" />
                                <x-form.input name="deadline" :value="old('deadline', $project->deadline?->format('Y-m-d'))" type="date" />
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 flex gap-3">
                    <x-form.button type="submit" class="flex-1 justify-center flex text-lg py-3 shadow-sm">
                        Cập nhật
                    </x-form.button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
