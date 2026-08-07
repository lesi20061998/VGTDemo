@extends('superadmin.layouts.app')
@section('title', 'Tạo Dự án mới | Super Admin')
@section('page-title', 'Tạo Dự án từ Hợp đồng')

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

    <form method="POST" action="{{ route('superadmin.projects.store') }}" id="create-form">
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
                        Thông tin Dự án
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-form.label value="Mã Dự án" required="true" />
                                <x-form.input name="code" :value="old('code')" placeholder="VD: PRJ001" required="true" />
                            </div>
                            <div>
                                <x-form.label value="Tên Dự án" required="true" />
                                <x-form.input name="name" :value="old('name')" required="true" />
                            </div>
                        </div>
                        
                        <div>
                            <x-form.label value="Hợp đồng liên quan (Đã duyệt)" required="true" />
                            <x-form.select name="contract_id" required="true">
                                <option value="">-- Chọn Hợp đồng --</option>
                                @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}" {{ old('contract_id') == $contract->id ? 'selected' : '' }}>
                                    {{ $contract->title }} - {{ $contract->client_name }}
                                </option>
                                @endforeach
                            </x-form.select>
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
                            <x-form.label value="Feature Packs (Tính năng mở rộng)" />
                            <div class="mt-2 space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                @if($featurePacks->isNotEmpty())
                                    @php
                                        $groupedPacks = $featurePacks->groupBy('group_name');
                                    @endphp
                                    @foreach($groupedPacks as $groupName => $packs)
                                        <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                            <h4 class="font-semibold text-gray-700 text-sm mb-2">{{ $groupName ?: 'Khác' }}</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                @foreach($packs as $pack)
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" name="cms_features[]" value="{{ $pack->code }}" class="rounded border-gray-300 text-[#001B4E] shadow-sm focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50" {{ (is_array(old('cms_features')) && in_array($pack->code, old('cms_features'))) ? 'checked' : '' }}>
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
                            <x-form.textarea name="environment" :value="old('environment')" rows="3" placeholder="Thông tin Server, Domain, Database, FTP..." />
                        </div>
                        
                        <div>
                            <x-form.label value="Ghi chú thêm" />
                            <x-form.textarea name="notes" :value="old('notes')" rows="3" placeholder="Các ghi chú hoặc yêu cầu đặc biệt khác..." />
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
                            <x-form.label value="Admin phụ trách (PM)" required="true" />
                            <x-form.select name="employee_id" required="true">
                                <option value="">-- Chọn Admin --</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    [{{ $employee->code ?? 'N/A' }}] {{ $employee->name }}
                                </option>
                                @endforeach
                            </x-form.select>
                        </div>
                        
                        <div>
                            <x-form.label value="Developer phụ trách" />
                            <select name="dev_ids[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#001B4E] focus:ring-[#001B4E] sm:text-sm" style="min-height: 100px;">
                                @foreach($devs as $dev)
                                <option value="{{ $dev->id }}" {{ in_array($dev->id, old('dev_ids', [])) ? 'selected' : '' }}>
                                    [{{ $dev->code ?? 'N/A' }}] {{ $dev->name }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1 italic">Nhấn giữ Ctrl/Cmd để chọn nhiều Dev</p>
                        </div>
                        
                        <div>
                            <x-form.label value="Trạng thái Dự án" />
                            <x-form.select name="status" :options="[
                                'pending' => 'Pending (Đang chờ)', 
                                'active' => 'Active (Hoạt động)', 
                                'assigned' => 'Assigned (Đã phân Dev)', 
                                'in_progress' => 'In Progress (Đang làm)', 
                                'on_hold' => 'On Hold (Tạm ngưng)', 
                                'error' => 'Error (Có lỗi)',
                                'completed' => 'Completed (Hoàn thành)'
                            ]" :value="old('status', 'pending')" />
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <x-form.button type="submit" class="w-full justify-center flex text-lg py-3">
                        Tạo & Phân Phối
                    </x-form.button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
