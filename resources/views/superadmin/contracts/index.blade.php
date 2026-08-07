@extends('superadmin.layouts.app')

@section('title', 'Quản lý Hợp đồng | Super Admin')
@section('page-title', 'Quản lý Hợp đồng')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Danh sách Hợp đồng</h1>
            <p class="text-gray-500 mt-1">Quản lý và theo dõi hợp đồng theo nhóm dịch vụ</p>
        </div>
        <div>
            <a href="{{ route('superadmin.contracts.create') }}" class="px-6 py-3 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium inline-flex items-center transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm Hợp đồng mới
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ route('superadmin.contracts.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <select name="service_type" class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50">
                    <option value="">-- Tất cả Nhóm Dịch vụ --</option>
                    <option value="website" {{ request('service_type') == 'website' ? 'selected' : '' }}>Thiết kế website</option>
                    <option value="publication" {{ request('service_type') == 'publication' ? 'selected' : '' }}>Thiết kế ấn phẩm</option>
                    <option value="branding" {{ request('service_type') == 'branding' ? 'selected' : '' }}>Thiết kế nhận diện thương hiệu</option>
                    <option value="social_media" {{ request('service_type') == 'social_media' ? 'selected' : '' }}>Sản xuất nội dung mạng xã hội</option>
                </select>
            </div>
            <div class="flex-1">
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50">
                    <option value="">-- Tất cả Trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ (Pending)</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hiệu lực (Active)</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành (Completed)</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Lọc</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Hợp đồng</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Khách hàng</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Dịch vụ</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Thời hạn</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Tài nguyên (Web)</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Trạng thái</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $contract->id }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-[#001B4E]">{{ $contract->title }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($contract->contract_value ?? 0) }} VNĐ</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $contract->client_name }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $services = [
                                    'website' => ['text' => 'Website', 'color' => 'bg-blue-100 text-blue-800'],
                                    'publication' => ['text' => 'Ấn phẩm', 'color' => 'bg-purple-100 text-purple-800'],
                                    'branding' => ['text' => 'Thương hiệu', 'color' => 'bg-amber-100 text-amber-800'],
                                    'social_media' => ['text' => 'Mạng xã hội', 'color' => 'bg-teal-100 text-teal-800'],
                                    'other' => ['text' => 'Khác', 'color' => 'bg-gray-100 text-gray-800'],
                                ];
                                $service = $services[$contract->service_type] ?? $services['other'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service['color'] }}">
                                {{ $service['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-600">Bắt đầu: {{ $contract->start_date ? $contract->start_date->format('d/m/Y') : '-' }}</div>
                            <div class="text-xs text-gray-600 mt-1">Kết thúc: <span class="font-medium {{ $contract->end_date && $contract->end_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : '-' }}</span></div>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            @if($contract->service_type === 'website' || $contract->domain_name)
                                <div class="mb-1">
                                    <span class="font-semibold">Domain:</span> 
                                    <a href="http://{{ $contract->domain_name }}" target="_blank" class="text-[#001B4E] hover:underline">{{ $contract->domain_name ?: 'Chưa cập nhật' }}</a>
                                </div>
                                <div class="text-gray-500">Mua lúc: {{ $contract->domain_purchase_date ? $contract->domain_purchase_date->format('d/m/Y') : '-' }}</div>
                            @else
                                <span class="text-gray-400 italic">Không áp dụng</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statuses = [
                                    'pending' => ['text' => 'Đang chờ', 'color' => 'bg-yellow-100 text-yellow-800'],
                                    'active' => ['text' => 'Đang hiệu lực', 'color' => 'bg-green-100 text-green-800'],
                                    'completed' => ['text' => 'Đã hoàn thành', 'color' => 'bg-blue-100 text-blue-800'],
                                    'cancelled' => ['text' => 'Đã hủy', 'color' => 'bg-red-100 text-red-800'],
                                ];
                                $status = $statuses[$contract->status] ?? $statuses['pending'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['color'] }}">
                                {{ $status['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="openContractModal({{ $contract->id }})" class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition-colors" title="Xem chi tiết">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Hidden data for modal -->
                                <div id="contract-data-{{ $contract->id }}" class="hidden">
                                    <div data-status="{{ $contract->status }}"></div>
                                    <div class="modal-html-content">
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
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $status['color'] }}">
                                                                {{ $status['text'] }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-500 text-sm block mb-1">Nhóm dịch vụ:</span>
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('superadmin.contracts.edit', $contract->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('superadmin.contracts.destroy', $contract->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này không? Dữ liệu hình ảnh hợp đồng cũng sẽ bị xóa vĩnh viễn.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="h-12 w-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900">Không có dữ liệu hợp đồng</p>
                                <p class="text-sm mt-1">Chưa có hợp đồng nào được tạo trong hệ thống.</p>
                                <a href="{{ route('superadmin.contracts.create') }}" class="mt-4 text-[#001B4E] hover:underline font-medium">Thêm hợp đồng đầu tiên</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $contracts->links() }}
        </div>
    </div>
</div>

<!-- Modal Xem nhanh Hợp đồng -->
<div id="contractModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeContractModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                <!-- Header -->
                <div class="bg-[#001B4E] px-4 py-4 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-white" id="modal-title">Chi tiết Hợp đồng</h3>
                    <button type="button" class="text-gray-300 hover:text-white" onclick="closeContractModal()">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Content -->
                <div id="modal-contract-body" class="bg-gray-50 px-4 pb-4 pt-5 sm:p-6 sm:pb-4 max-h-[75vh] overflow-y-auto w-full">
                    <!-- Dynamic content will be injected here -->
                </div>
                <!-- Footer -->
                <div class="bg-white px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                    <a href="#" id="modal-btn-create-project" class="hidden inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                        Tạo Dự án
                    </a>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeContractModal()">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openContractModal(id) {
        const dataContainer = document.getElementById('contract-data-' + id);
        if (!dataContainer) return;
        
        const infoDiv = dataContainer.querySelector('div[data-status]');
        const status = infoDiv.getAttribute('data-status');
        
        const htmlContent = dataContainer.querySelector('.modal-html-content').innerHTML;
        
        document.getElementById('modal-contract-body').innerHTML = htmlContent;
        
        const btnCreateProject = document.getElementById('modal-btn-create-project');
        if (status === 'active' || status === 'completed') {
            btnCreateProject.classList.remove('hidden');
            btnCreateProject.href = "{{ route('superadmin.projects.create') }}?contract_id=" + id;
        } else {
            btnCreateProject.classList.add('hidden');
        }
        
        document.getElementById('contractModal').classList.remove('hidden');
    }

    function closeContractModal() {
        document.getElementById('contractModal').classList.add('hidden');
        document.getElementById('modal-contract-body').innerHTML = ''; // clear for next click
    }
</script>
@endsection
