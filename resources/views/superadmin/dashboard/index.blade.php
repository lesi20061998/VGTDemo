@extends('superadmin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Super Admin')

@section('content')

@if(isset($infectedProjects) && count($infectedProjects) > 0)
<div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-lg shadow-sm mb-6">
    <div class="flex items-center">
        <svg class="w-8 h-8 mr-4 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <div>
            <h3 class="font-bold text-red-800 text-lg">CẢNH BÁO BẢO MẬT NGHIÊM TRỌNG!</h3>
            <p class="text-red-700 mb-2">Hệ thống phát hiện <strong>{{ count($infectedProjects) }} dự án</strong> có dấu hiệu bị chèn mã độc. Yêu cầu kiểm tra ngay:</p>
            <ul class="list-disc list-inside text-red-800 font-medium">
                @foreach($infectedProjects as $project)
                <li>
                    Dự án {{ $project->code }} ({{ $project->name }}) - 
                    <a href="{{ url('/superadmin/projects/'.$project->id.'/config') }}" class="underline hover:text-red-900">Vào Lịch sử dự án</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 mb-6 text-white">
    <h2 class="text-2xl font-bold">
        Xin chào, {{ auth()->user()->employee->position ?? 'Nhân viên' }} {{ auth()->user()->name }}!
    </h2>
    <p class="text-blue-100 mt-2">Chào mừng bạn quay trở lại hệ thống quản trị</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Tổng Nhân sự</p>
                <p class="text-3xl font-bold text-blue-600">{{ $totalEmployees }}</p>
            </div>
            <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Tổng Hợp đồng</p>
                <p class="text-3xl font-bold text-blue-600">{{ $totalContracts }}</p>
            </div>
            <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">HĐ Chờ duyệt</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $pendingContracts }}</p>
            </div>
            <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Dự án hoạt động</p>
                <p class="text-3xl font-bold text-green-600">{{ $activeProjects }}/{{ $totalProjects }}</p>
            </div>
            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Doanh thu dự kiến tháng</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($expectedRevenue) }} đ</p>
            </div>
            <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Dự án sắp trễ hạn -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Dự án sắp trễ hạn / Quá hạn
            </h2>
            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($urgentProjects) }} dự án</span>
        </div>
        
        @if(count($urgentProjects) > 0)
            <div class="space-y-4">
                @foreach($urgentProjects as $project)
                    @php
                        $daysLeft = now()->diffInDays($project->deadline, false);
                    @endphp
                    <div class="p-4 bg-red-50 rounded-lg border border-red-100 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-red-800">{{ $project->name }}</p>
                            <p class="text-sm text-red-600">Client: {{ $project->client_name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $daysLeft < 0 ? 'bg-red-600 text-white' : 'bg-red-200 text-red-800' }}">
                                @if($daysLeft < 0)
                                    Quá hạn {{ abs(intval($daysLeft)) }} ngày
                                @elseif($daysLeft == 0)
                                    Hạn chót hôm nay
                                @else
                                    Còn {{ intval($daysLeft) }} ngày
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Tuyệt vời! Không có dự án nào đang sắp trễ hạn.</p>
        @endif
    </div>

    <!-- Tiến độ dự án -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Tiến độ dự án (Task)
            </h2>
            <a href="{{ route('superadmin.projects.index') }}" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3">Dự án</th>
                        <th scope="col" class="px-4 py-3">Tiến độ</th>
                        <th scope="col" class="px-4 py-3 text-right">Tình trạng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projectProgresses as $project)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $project->name }}
                                <div class="text-xs text-gray-500 mt-1">Deadline: {{ $project->deadline ? $project->deadline->format('d/m/Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs font-medium text-blue-700">{{ $project->progress }}%</span>
                                    <span class="text-xs font-medium text-gray-500">{{ $project->completedTasks }}/{{ $project->totalTasks }} tasks</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($project->progress == 100 && $project->totalTasks > 0)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Hoàn thành</span>
                                @elseif($project->progress > 0)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Đang làm</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Chưa bắt đầu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">Chưa có dự án nào đang hoạt động.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-6">
    <!-- Tài nguyên Web sắp hết hạn -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>
                Tài nguyên Web (Domain/Hosting) sắp hết hạn
            </h2>
            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($expiringWebResources) }} tài nguyên</span>
        </div>

        @if(count($expiringWebResources) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3">Hợp đồng / Khách hàng</th>
                            <th scope="col" class="px-4 py-3">Domain</th>
                            <th scope="col" class="px-4 py-3">Hosting</th>
                            <th scope="col" class="px-4 py-3 text-right">Ngày hết hạn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringWebResources as $resource)
                            @php
                                $daysLeft = now()->diffInDays($resource->end_date, false);
                            @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $resource->title }}</div>
                                    <div class="text-xs text-gray-500">Khách hàng: {{ $resource->client_name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $resource->domain_name ?: '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $resource->hosting_provider ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="font-medium text-gray-900 mb-1">{{ $resource->end_date->format('d/m/Y') }}</div>
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold {{ $daysLeft < 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        @if($daysLeft < 0)
                                            Quá hạn {{ abs(intval($daysLeft)) }} ngày
                                        @elseif($daysLeft == 0)
                                            Hôm nay
                                        @else
                                            Còn {{ intval($daysLeft) }} ngày
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Chưa có tài nguyên Domain/Hosting nào sắp hết hạn trong 30 ngày tới.</p>
        @endif
    </div>
</div>

@endsection
