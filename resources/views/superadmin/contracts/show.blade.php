@extends('superadmin.layouts.app')

@section('title', 'Chi tiết Hợp đồng')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Chi tiết Hợp đồng</h1>
        <p class="text-gray-500 text-sm mt-1">Xem thông tin chi tiết và yêu cầu nghiệp vụ của hợp đồng</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('superadmin.contracts.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            Quay lại
        </a>
        <a href="{{ route('superadmin.contracts.edit', $contract->id) }}" class="px-4 py-2 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] transition-colors">
            Chỉnh sửa
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Thông tin Hợp đồng</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-500 text-sm block">Tên hợp đồng:</span>
                    <span class="font-semibold">{{ $contract->title }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">Tên khách hàng:</span>
                    <span class="font-semibold">{{ $contract->client_name ?: 'Chưa cập nhật' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">Giá trị:</span>
                    <span class="font-semibold text-green-600">{{ number_format($contract->contract_value ?? 0) }} VNĐ</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">Thời gian:</span>
                    <span class="font-semibold">
                        {{ $contract->start_date ? $contract->start_date->format('d/m/Y') : '?' }} 
                        - 
                        {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : '?' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Requirements -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Yêu cầu Kỹ thuật & Tính năng</h3>
            
            <div class="space-y-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Yêu cầu Kỹ thuật</h4>
                    <div class="prose max-w-none text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        {!! $contract->technical_requirements ?: '<span class="italic text-gray-400">Không có dữ liệu</span>' !!}
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Các tính năng chính</h4>
                    <div class="prose max-w-none text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        {!! $contract->features ?: '<span class="italic text-gray-400">Không có dữ liệu</span>' !!}
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Ghi chú chi tiết</h4>
                    <div class="prose max-w-none text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        {!! $contract->description ?: '<span class="italic text-gray-400">Không có dữ liệu</span>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Status & Type -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Phân loại & Trạng thái</h3>
            <div class="space-y-4">
                <div>
                    <span class="text-gray-500 text-sm block mb-1">Trạng thái:</span>
                    @php
                        $statuses = [
                            'pending' => ['text' => 'Đang chờ', 'color' => 'bg-yellow-100 text-yellow-800'],
                            'active' => ['text' => 'Đang hiệu lực / Đã duyệt', 'color' => 'bg-green-100 text-green-800'],
                            'completed' => ['text' => 'Đã hoàn thành', 'color' => 'bg-blue-100 text-blue-800'],
                            'cancelled' => ['text' => 'Đã hủy', 'color' => 'bg-red-100 text-red-800'],
                        ];
                        $status = $statuses[$contract->status] ?? $statuses['pending'];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $status['color'] }}">
                        {{ $status['text'] }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block mb-1">Nhóm dịch vụ:</span>
                    @php
                        $services = [
                            'website' => ['text' => 'Thiết kế website', 'color' => 'bg-blue-100 text-blue-800'],
                            'publication' => ['text' => 'Thiết kế ấn phẩm', 'color' => 'bg-purple-100 text-purple-800'],
                            'branding' => ['text' => 'Thiết kế nhận diện thương hiệu', 'color' => 'bg-amber-100 text-amber-800'],
                            'social_media' => ['text' => 'Sản xuất nội dung mạng xã hội', 'color' => 'bg-teal-100 text-teal-800'],
                            'other' => ['text' => 'Khác', 'color' => 'bg-gray-100 text-gray-800'],
                        ];
                        $service = $services[$contract->service_type] ?? $services['other'];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $service['color'] }}">
                        {{ $service['text'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Resources -->
        @if($contract->service_type === 'website' || $contract->domain_name)
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Tài nguyên Web</h3>
            <div class="space-y-4">
                <div>
                    <span class="text-gray-500 text-sm block">Domain:</span>
                    <a href="http://{{ $contract->domain_name }}" target="_blank" class="font-medium text-[#001B4E] hover:underline">{{ $contract->domain_name ?: 'Chưa cập nhật' }}</a>
                    @if($contract->domain_purchase_date)
                    <div class="text-xs text-gray-400 mt-1">Mua lúc: {{ $contract->domain_purchase_date->format('d/m/Y') }}</div>
                    @endif
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">Hosting/Máy chủ:</span>
                    <span class="font-medium">{{ $contract->hosting_provider ?: 'Chưa cập nhật' }}</span>
                    @if($contract->hosting_start_date)
                    <div class="text-xs text-gray-400 mt-1">Bắt đầu: {{ $contract->hosting_start_date->format('d/m/Y') }}</div>
                    @endif
                </div>
                
                @if($contract->has_client_resources)
                <div class="pt-4 border-t border-gray-100">
                    <span class="font-bold text-gray-800 text-sm block mb-2">Tài nguyên do khách gửi:</span>
                    <div class="prose max-w-none text-gray-600 bg-amber-50 p-3 rounded-lg border border-amber-100 text-sm">
                        {!! $contract->client_resource_details ?: '<span class="italic text-gray-400">Không có dữ liệu</span>' !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Action Create Project -->
        @if($contract->status === 'active' || $contract->status === 'completed')
        <div class="bg-blue-50 rounded-xl shadow-sm p-6 border border-blue-100">
            <h3 class="font-bold text-[#001B4E] mb-2">Triển khai Dự án</h3>
            <p class="text-sm text-gray-600 mb-4">Hợp đồng này đã được duyệt. Bạn có thể tiến hành tạo dự án.</p>
            <a href="{{ route('superadmin.projects.create') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tạo Dự án Mới
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
