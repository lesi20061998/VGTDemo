@extends('superadmin.layouts.app')

@section('title', 'Chỉnh sửa Hợp đồng | Super Admin')
@section('page-title', 'Cập nhật Hợp đồng')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.contracts.index') }}" class="text-gray-500 hover:text-[#001B4E] inline-flex items-center transition-colors">
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

    <form action="{{ route('superadmin.contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Thông tin cơ bản
                        </h3>
                        <span class="text-xs font-semibold text-gray-400">ID: #{{ $contract->id }}</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Tên hợp đồng" required="true" />
                            <x-form.input name="title" :value="old('title', $contract->title)" required="true" />
                        </div>
                        
                        <div>
                            <x-form.label value="Tên khách hàng" />
                            <x-form.input name="client_name" :value="old('client_name', $contract->client_name)" />
                        </div>
                        
                        <div>
                            <x-form.label value="Giá trị hợp đồng (VNĐ)" />
                            <x-form.input name="contract_value" :value="old('contract_value', $contract->contract_value)" type="number" step="1000" min="0" />
                        </div>
                        
                        <div>
                            <x-form.label value="Yêu cầu Kỹ thuật & Tính năng" />
                            <x-form.rich-editor name="technical_requirements" :value="old('technical_requirements', $contract->technical_requirements)" />
                        </div>

                        <div>
                            <x-form.label value="Các tính năng chính" />
                            <x-form.rich-editor name="features" :value="old('features', $contract->features)" />
                        </div>

                        <div>
                            <x-form.label value="Ghi chú chi tiết" />
                            <x-form.rich-editor name="description" :value="old('description', $contract->description)" />
                        </div>
                    </div>
                </div>

                <!-- Web Resources Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100" id="web-resources-card">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        Tài nguyên Web (Domain & Hosting)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-700">Tên miền (Domain)</h4>
                            <div>
                                <x-form.label value="Tên miền" />
                                <x-form.input name="domain_name" :value="old('domain_name', $contract->domain_name)" />
                            </div>
                            <div>
                                <x-form.label value="Ngày mua/Kích hoạt" />
                                <x-form.input type="date" name="domain_purchase_date" :value="old('domain_purchase_date', $contract->domain_purchase_date?->format('Y-m-d'))" />
                            </div>
                        </div>
                        
                        <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-700">Máy chủ (Hosting/VPS)</h4>
                            <div>
                                <x-form.label value="Nhà cung cấp" />
                                <x-form.input name="hosting_provider" :value="old('hosting_provider', $contract->hosting_provider)" />
                            </div>
                            <div>
                                <x-form.label value="Ngày mua/Kích hoạt" />
                                <x-form.input type="date" name="hosting_start_date" :value="old('hosting_start_date', $contract->hosting_start_date?->format('Y-m-d'))" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" name="has_client_resources" id="has_client_resources" value="1" {{ old('has_client_resources', $contract->has_client_resources) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="has_client_resources" class="ml-2 text-sm font-medium text-gray-900">Khách hàng cung cấp Tài khoản Domain/Hosting có sẵn</label>
                        </div>
                        
                        <div id="client_resources_wrapper" class="{{ old('has_client_resources', $contract->has_client_resources) ? '' : 'hidden' }}">
                            <x-form.label value="Chi tiết Tài khoản / Nguồn tài nguyên khách gửi" />
                            <x-form.rich-editor name="client_resource_details" :value="old('client_resource_details', $contract->client_resource_details)" />
                            <p class="text-xs text-gray-500 mt-1 italic">Vui lòng cung cấp link đăng nhập, user, pass nếu có...</p>
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
                        Phân loại & Thời gian
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-form.label value="Nhóm dịch vụ" required="true" />
                            <x-form.select name="service_type" required="true" id="service_type" :options="[
                                'website' => 'Thiết kế website',
                                'publication' => 'Thiết kế ấn phẩm',
                                'branding' => 'Thiết kế nhận diện thương hiệu',
                                'social_media' => 'Sản xuất nội dung mạng xã hội',
                            ]" :value="old('service_type', $contract->service_type)" />
                        </div>
                        
                        <div>
                            <x-form.label value="Trạng thái" required="true" />
                            <x-form.select name="status" required="true" :options="[
                                'pending' => 'Đang chờ ký / Setup',
                                'active' => 'Đang tiến hành',
                                'completed' => 'Đã hoàn thành',
                                'cancelled' => 'Đã hủy',
                            ]" :value="old('status', $contract->status)" />
                        </div>
                        
                        <div class="pt-2 border-t">
                            <x-form.label value="Ngày bắt đầu" />
                            <x-form.input type="date" name="start_date" :value="old('start_date', $contract->start_date?->format('Y-m-d'))" />
                        </div>
                        
                        <div>
                            <x-form.label value="Ngày kết thúc (Dự kiến)" />
                            <x-form.input type="date" name="end_date" :value="old('end_date', $contract->end_date?->format('Y-m-d'))" />
                        </div>
                    </div>
                </div>
                
                <!-- Attachments Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        Chứng từ & Hình ảnh
                    </h3>
                    
                    @if($contract->attachments && count($contract->attachments) > 0)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Ảnh đã tải lên:</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($contract->attachments as $path)
                                <div class="relative group rounded-md overflow-hidden border border-gray-200 aspect-square">
                                    <img src="{{ Storage::url($path) }}" alt="Contract image" class="object-cover w-full h-full">
                                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <label class="cursor-pointer text-white flex items-center">
                                            <input type="checkbox" name="remove_attachments[]" value="{{ $path }}" class="mr-2 rounded text-red-500 focus:ring-red-500">
                                            <span class="text-xs font-medium">Xóa</span>
                                        </label>
                                    </div>
                                    <a href="{{ Storage::url($path) }}" target="_blank" class="absolute top-1 right-1 bg-white rounded-full p-1 shadow-sm text-gray-500 hover:text-[#001B4E] opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Đánh dấu check để xóa ảnh cũ khi lưu.</p>
                        </div>
                        <hr class="my-3">
                    @endif
                    
                    <div>
                        <x-form.label value="Thêm hình ảnh mới" />
                        <input type="file" name="attachment_files[]" multiple accept="image/*" class="w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-lg file:border-0
                            file:text-sm file:font-semibold
                            file:bg-[#001B4E] file:text-white
                            hover:file:bg-[#002D80]
                            cursor-pointer border rounded-lg border-gray-300 p-2 mt-1">
                    </div>
                </div>
                
                <div class="pt-4 flex gap-4">
                    @if($contract->status !== 'active' && $contract->status !== 'completed')
                    <button type="submit" name="action" value="approve" class="flex-1 justify-center flex items-center text-lg py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Duyệt Hợp đồng
                    </button>
                    @endif
                    <x-form.button type="submit" class="flex-1 justify-center flex text-lg py-3">
                        Lưu Cập Nhật
                    </x-form.button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serviceType = document.getElementById('service_type');
        const webCard = document.getElementById('web-resources-card');
        
        function toggleWebCard() {
            if(serviceType.value === 'website') {
                webCard.style.opacity = '1';
                webCard.style.pointerEvents = 'auto';
                webCard.style.filter = 'none';
            } else {
                webCard.style.opacity = '0.5';
                webCard.style.filter = 'grayscale(100%)';
            }
        }
        
        serviceType.addEventListener('change', toggleWebCard);
        toggleWebCard(); // init
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('has_client_resources');
        const wrapper = document.getElementById('client_resources_wrapper');
        
        if (checkbox && wrapper) {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    wrapper.classList.remove('hidden');
                } else {
                    wrapper.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
