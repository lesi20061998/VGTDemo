@extends('superadmin.layouts.app')

@section('title', 'Quản lý Task')
@section('page-title', 'Quản lý Task')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-600">Quản lý công việc và nhiệm vụ</p>
    <a href="{{ route('superadmin.tasks.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
        + Tạo Task mới
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dự án</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người thực hiện</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ưu tiên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hạn</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tasks as $task)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $task->project->name ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $task->assignedTo->name ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $task->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $task->due_date?->format('d/m/Y') ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('superadmin.tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900 mr-3">Sửa</a>
                    <form action="{{ route('superadmin.tasks.destroy', $task) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Xác nhận xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Chưa có task nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $tasks->links() }}
</div>
@endsection
