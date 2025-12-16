@extends('superadmin.layouts.app')

@section('title', 'Ticket & Feedback')
@section('page-title', 'Quản lý Ticket & Feedback')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-600">Quản lý ticket hỗ trợ và feedback dự án</p>
    <a href="{{ route('superadmin.tickets.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
        + Tạo Ticket mới
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã Ticket</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dự án</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ưu tiên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người xử lý</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tickets as $ticket)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-mono text-sm font-semibold text-purple-600">{{ $ticket->ticket_number }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $ticket->title }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $ticket->project->name ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $ticket->type === 'feedback' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $ticket->type === 'support' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $ticket->type === 'bug' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $ticket->type === 'feature' ? 'bg-purple-100 text-purple-800' : '' }}">
                        {{ ucfirst($ticket->type) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $ticket->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $ticket->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $ticket->assignedTo->name ?? 'Chưa phân công' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('superadmin.tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-900 mr-3">Xem</a>
                    <a href="{{ route('superadmin.tickets.edit', $ticket) }}" class="text-purple-600 hover:text-purple-900">Sửa</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500">Chưa có ticket nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $tickets->links() }}
</div>
@endsection
