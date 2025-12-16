@extends('superadmin.layouts.app')

@section('title', 'Chi tiết Dự án')
@section('page-title', 'Chi tiết Dự án')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h2>
        <div class="flex gap-2">
            @if($project->status == 'pending')
            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg">
                Chờ duyệt
            </span>
            @elseif($project->status == 'assigned')
            <form method="POST" action="{{ route('superadmin.projects.create-website', $project) }}">
                @csrf
                <button type="submit" onclick="return confirm('Tạo website cho dự án này?')"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Tạo Website
                </button>
            </form>
            @endif
            <a href="{{ route('superadmin.projects.edit', $project) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Sửa</a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Thông tin Dự án</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-gray-600">Mã dự án:</span> <span class="font-mono font-bold text-purple-600">{{ $project->code }}</span></div>
                    <div><span class="text-gray-600">Trạng thái:</span> 
                        <span class="px-3 py-1 text-sm rounded-full {{ $project->status == 'active' ? 'bg-green-100 text-green-800' : ($project->status == 'assigned' ? 'bg-blue-100 text-blue-800' : ($project->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    <div class="col-span-2"><span class="text-gray-600">Subdomain:</span> <span class="font-mono font-bold text-blue-600">{{ $project->subdomain }}</span></div>
                    <div><span class="text-gray-600">Khách hàng:</span> {{ $project->client_name }}</div>
                    <div><span class="text-gray-600">Giá trị HĐ:</span> {{ number_format($project->contract_value ?? 0) }} VNĐ</div>
                    <div><span class="text-gray-600">Ngày bắt đầu:</span> {{ $project->start_date->format('d/m/Y') }}</div>
                    <div>
                        <span class="text-gray-600">Deadline:</span> 
                        <span class="font-bold text-red-600">{{ $project->deadline->format('d/m/Y') }}</span>
                        @php
                            $daysLeft = (int) now()->diffInDays($project->deadline, false);
                        @endphp
                        @if($daysLeft >= 0)
                            <span class="ml-2 text-xs px-2 py-1 rounded-full {{ $daysLeft <= 7 ? 'bg-red-100 text-red-800' : ($daysLeft <= 30 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $daysLeft }} ngày còn lại
                            </span>
                        @else
                            <span class="ml-2 text-xs px-2 py-1 rounded-full bg-red-100 text-red-800">
                                Quá hạn {{ abs($daysLeft) }} ngày
                            </span>
                        @endif
                    </div>
                    <div><span class="text-gray-600">Người tạo:</span> {{ $project->createdBy?->name ?? 'N/A' }}</div>
                    <div><span class="text-gray-600">Admin phụ trách:</span> {{ $project->admin?->name ?? 'Chưa phân' }}</div>
                </div>
            </div>

            @if($project->status == 'active')
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-green-900 mb-4">Thông tin Truy cập</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-600">Login URL:</span>
                        <a href="{{ url('/' . $project->code . '/login') }}" target="_blank"
                           class="font-mono font-bold text-green-700 hover:underline flex items-center gap-2">
                            {{ url('/' . $project->code . '/login') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Admin Panel:</span>
                        <p class="font-mono text-gray-700">{{ url('/' . $project->code . '/admin') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Username:</span>
                        <p class="font-mono font-bold">{{ $project->project_admin_username }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Password:</span>
                        <p class="font-mono font-bold text-red-600">{{ $project->project_admin_password }}</p>
                        <p class="text-xs text-gray-500 mt-1">⚠️ Lưu mật khẩu này, không thể xem lại sau khi rời trang</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Yêu cầu Kỹ thuật</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Yêu cầu kỹ thuật:</p>
                        <p class="text-gray-600">{{ $project->technical_requirements ?? 'Không có' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Tính năng:</p>
                        <p class="text-gray-600">{{ $project->features ?? 'Không có' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Môi trường:</p>
                        <p class="text-gray-600">{{ $project->environment ?? 'Không có' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Ghi chú:</p>
                        <p class="text-gray-600">{{ $project->notes ?? 'Không có' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Tạo dự án</p>
                            <p class="text-xs text-gray-500">{{ $project->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($project->approved_at)
                    <div class="flex items-start">
                        <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Đã duyệt</p>
                            <p class="text-xs text-gray-500">{{ $project->approved_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($project->initialized_at)
                    <div class="flex items-start">
                        <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Đã khởi tạo</p>
                            <p class="text-xs text-gray-500">{{ $project->initialized_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
