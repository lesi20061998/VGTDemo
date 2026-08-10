@extends('superadmin.layouts.app')
@section('title', 'Quản lý Dự án | Super Admin')
@section('page-title', 'Quản lý Dự án (Projects)')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Danh sách Dự án</h1>
            <p class="text-gray-500 mt-1">Quản lý và theo dõi tiến độ các dự án đang triển khai</p>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage-projects'))
        <div>
            <a href="{{ route('superadmin.projects.create') }}" class="px-6 py-3 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium inline-flex items-center transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo Dự án
            </a>
        </div>
        @endif
    </div>

    @if(isset($infectedProjects) && count($infectedProjects) > 0)
    <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-8 h-8 mr-4 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
            <h3 class="font-bold text-red-800 text-lg">CẢNH BÁO BẢO MẬT NGHIÊM TRỌNG!</h3>
            <p class="text-red-700 mb-2">Hệ thống phát hiện <strong>{{ count($infectedProjects) }} dự án</strong> có dấu hiệu bị chèn mã độc. Yêu cầu kiểm tra ngay:</p>
            <ul class="list-disc list-inside text-red-800 font-medium">
                @foreach($projects->whereIn('id', $infectedProjects) as $project)
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

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Mã DA</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Tên dự án</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Khách hàng</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Admin phụ trách</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Deadline</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                <span class="font-mono font-bold text-[#001B4E]">{{ $project->code }}</span>
                                @if(isset($infectedProjects) && in_array($project->id, $infectedProjects))
                                <span title="Có dấu hiệu bị tấn công mã độc!" class="ml-2 inline-flex items-center justify-center p-1 bg-red-100 text-red-600 rounded-full animate-bounce">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-gray-900">{{ $project->name }}</div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">{{ $project->client_name }}</td>
                        <td class="py-4 px-6 text-gray-600">
                            <div class="flex items-center">
                                @if($project->admin)
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold mr-2 uppercase">
                                    {{ substr($project->admin->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium">{{ $project->admin->name }}</span>
                                @else
                                <span class="text-sm text-gray-400 italic">Chưa phân</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $project->deadline->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $project->status == 'active' ? 'bg-green-100 text-green-800' : 
                                   ($project->status == 'assigned' ? 'bg-blue-100 text-blue-800' : 
                                   ($project->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($project->status == 'error' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('superadmin.projects.show', $project) }}" class="p-2 text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors" title="Xem chi tiết">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage-projects'))
                                <a href="{{ route('superadmin.projects.edit', $project) }}" class="p-2 text-[#001B4E] bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors" title="Chỉnh sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('superadmin.projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Dự án này? Toàn bộ dữ liệu liên quan sẽ bị ảnh hưởng.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">Chưa có Dự án nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($projects, 'hasPages') && $projects->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $projects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
