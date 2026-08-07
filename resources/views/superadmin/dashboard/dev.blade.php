@extends('superadmin.layouts.app')

@section('title', 'Developer Dashboard')
@section('page-title', 'Developer Dashboard')

@section('content')
<div class="bg-gradient-to-r from-teal-600 to-teal-800 rounded-lg shadow-lg p-6 mb-6 text-white">
    <h2 class="text-2xl font-bold">
        Xin chào, {{ auth()->user()->name }}!
    </h2>
    <p class="text-teal-100 mt-2">Chúc bạn một ngày làm việc thật hiệu quả và năng suất!</p>
</div>

<!-- Task Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Tổng Công việc Được giao</p>
                <p class="text-3xl font-bold text-gray-700">{{ $totalAssignedTasks }}</p>
            </div>
            <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Task Đang làm / Chờ xử lý</p>
                <p class="text-3xl font-bold text-blue-600">{{ $pendingTasks }}</p>
            </div>
            <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Task Hoàn thành</p>
                <p class="text-3xl font-bold text-green-600">{{ $completedTasks }}</p>
            </div>
            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <!-- Urgent Tasks -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-t-4 border-red-500">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Task Khẩn cấp / Trễ hạn
            </h2>
            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($urgentTasks) }} task</span>
        </div>
        
        @if(count($urgentTasks) > 0)
            <div class="space-y-4">
                @foreach($urgentTasks as $task)
                    <div class="p-4 rounded-lg bg-red-50 border border-red-100 flex justify-between items-center">
                        <div>
                            <a href="{{ route('superadmin.tasks.edit', $task->id) }}" class="font-bold text-red-700 hover:underline">
                                {{ $task->title }}
                            </a>
                            <p class="text-sm text-red-600 mt-1">Dự án: {{ $task->project->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded">
                                Deadline: {{ $task->deadline ? $task->deadline->format('d/m/Y') : 'Không có' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Tuyệt vời! Bạn không có task nào bị trễ hạn.</p>
            </div>
        @endif
        
        <div class="mt-4 pt-4 border-t">
            <a href="{{ route('superadmin.tasks.index') }}" class="text-teal-600 hover:text-teal-700 font-medium text-sm flex items-center">
                Xem tất cả Tasks
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <!-- Active Projects Progress -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-t-4 border-blue-500">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Tiến độ Dự án Đang tham gia
        </h2>
        
        @if(count($projectProgresses) > 0)
            <div class="space-y-6">
                @foreach($projectProgresses as $project)
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <a href="{{ route('superadmin.projects.show', $project) }}" class="font-bold text-gray-800 hover:text-blue-600">
                                    {{ $project->name }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">Hoàn thành {{ $project->completedTasks }}/{{ $project->totalTasks }} Tasks</p>
                            </div>
                            <span class="text-sm font-bold {{ $project->progress >= 100 ? 'text-green-600' : ($project->progress > 0 ? 'text-blue-600' : 'text-gray-500') }}">
                                {{ $project->progress }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $project->progress >= 100 ? 'bg-green-600' : 'bg-blue-600' }}" style="width: {{ $project->progress }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Chưa có dự án nào đang tham gia hoặc được giao Task.</p>
            </div>
        @endif
        
        <div class="mt-4 pt-4 border-t">
            <a href="{{ route('superadmin.projects.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center">
                Xem tất cả Dự án
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>
@endsection
