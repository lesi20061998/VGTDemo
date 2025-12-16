@extends('superadmin.layouts.app')

@section('title', 'Chi tiết Hợp đồng')
@section('page-title', 'Chi tiết Hợp đồng')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">{{ $contract->full_code }}</h2>
        <div class="flex gap-2">
            @if($contract->status == 'pending')
            <form method="POST" action="{{ route('superadmin.contracts.approve', $contract) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Duyệt Hợp đồng
                </button>
            </form>
            <form method="POST" action="{{ route('superadmin.contracts.reject', $contract) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Từ chối
                </button>
            </form>
            @endif
            <a href="{{ route('superadmin.contracts.edit', $contract) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Sửa</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Thông tin Hợp đồng</h3>
        <div class="grid grid-cols-2 gap-4">
            <div><span class="text-gray-600">Mã hợp đồng:</span> <span class="font-mono font-bold text-purple-600">{{ $contract->contract_code }}</span></div>
            <div><span class="text-gray-600">Trạng thái:</span> 
                <span class="px-3 py-1 text-sm rounded-full {{ $contract->status == 'approved' ? 'bg-green-100 text-green-800' : ($contract->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($contract->status) }}
                </span>
            </div>
            <div><span class="text-gray-600">Khách hàng:</span> {{ $contract->client_name }}</div>
            <div><span class="text-gray-600">Loại dịch vụ:</span> {{ $contract->service_type ?? '-' }}</div>
            <div><span class="text-gray-600">Account:</span> {{ $contract->employee->name }}</div>
            <div><span class="text-gray-600">Deadline:</span> {{ $contract->deadline?->format('d/m/Y') ?? '-' }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Yêu cầu & Mô tả</h3>
        <div class="space-y-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Yêu cầu nghiệp vụ:</p>
                <p class="text-gray-600 whitespace-pre-line">{{ trim($contract->requirements) ?: 'Không có' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Mô tả thiết kế:</p>
                <p class="text-gray-600 whitespace-pre-line">{{ trim($contract->design_description) ?: 'Không có' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Ghi chú nội bộ:</p>
                <p class="text-gray-600 whitespace-pre-line">{{ trim($contract->notes) ?: 'Không có' }}</p>
            </div>
            @if($contract->attachments)
            <div>
                <p class="text-sm font-medium text-gray-700">File đính kèm:</p>
                <a href="{{ Storage::url($contract->attachments) }}" target="_blank" 
                   class="text-blue-600 hover:underline">Xem file</a>
            </div>
            @endif
        </div>
    </div>

    @if($contract->status == 'approved')
        @php
            $existingProject = \App\Models\Project::where('contract_id', $contract->id)->first();
        @endphp
        
        @if($existingProject)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-2">Dự án đã được tạo</h3>
            <p class="text-blue-700 mb-3">Hợp đồng này đã có dự án: <strong>{{ $existingProject->name }}</strong></p>
            <div class="flex gap-2">
                <a href="{{ route('superadmin.projects.show', $existingProject) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Xem Dự án
                </a>
                @if($existingProject->status === 'active')
                <a href="{{ route('superadmin.projects.config', $existingProject) }}" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Cấu hình
                </a>
                @endif
            </div>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-green-900 mb-2">Hợp đồng đã được duyệt</h3>
            <p class="text-green-700">Có thể tạo dự án từ hợp đồng này.</p>
            <a href="{{ route('superadmin.projects.create') }}?contract_id={{ $contract->id }}" 
               class="mt-4 inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Tạo Dự án
            </a>
        </div>
        @endif
    @elseif($contract->status == 'pending')
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-bold text-yellow-900 mb-2">Hợp đồng chưa được duyệt</h3>
        <p class="text-yellow-700">Hợp đồng này cần được duyệt trước khi có thể tạo dự án.</p>
        <div class="mt-4 flex gap-2">
            <form method="POST" action="{{ route('superadmin.contracts.approve', $contract) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Duyệt Hợp đồng
                </button>
            </form>
            <form method="POST" action="{{ route('superadmin.contracts.reject', $contract) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" 
                        onclick="return confirm('Bạn có chắc chắn muốn từ chối hợp đồng này?')">
                    Từ chối
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
