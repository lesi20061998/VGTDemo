@extends('superadmin.layouts.app')

@section('title', 'Quản lý Dự án')
@section('page-title', 'Quản lý Dự án')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Danh sách Dự án</h1>
        <a href="{{ route('superadmin.projects.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tạo Dự án
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Mã DA</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Tên dự án</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Khách hàng</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Admin</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Deadline</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Trạng thái</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($projects as $project)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><span class="font-mono font-bold text-purple-600">{{ $project->code }}</span></td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $project->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $project->client_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $project->admin?->name ?? 'Chưa phân' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $project->deadline->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium 
                            {{ $project->status == 'active' ? 'bg-green-100 text-green-800' : 
                               ($project->status == 'assigned' ? 'bg-blue-100 text-blue-800' : 
                               ($project->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($project->status == 'error' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('superadmin.projects.show', $project) }}" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Xem chi tiết">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('superadmin.projects.edit', $project) }}" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('superadmin.projects.destroy', $project) }}" 
                                  onsubmit="return confirm('Xóa dự án này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Chưa có dự án nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
