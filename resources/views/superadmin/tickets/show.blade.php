@extends('superadmin.layouts.app')

@section('title', 'Chi tiết Ticket')
@section('page-title', 'Chi tiết Ticket')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.tickets.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại danh sách
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $ticket->title }}</h2>
                <p class="text-sm text-gray-600">Mã ticket: <span class="font-mono font-semibold text-purple-600">{{ $ticket->ticket_number }}</span></p>
            </div>
            <a href="{{ route('superadmin.tickets.edit', $ticket) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Chỉnh sửa
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="text-sm text-gray-600">Loại</label>
                <p class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $ticket->type === 'feedback' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $ticket->type === 'support' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $ticket->type === 'bug' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $ticket->type === 'feature' ? 'bg-purple-100 text-purple-800' : '' }}">
                        {{ ucfirst($ticket->type) }}
                    </span>
                </p>
            </div>

            <div>
                <label class="text-sm text-gray-600">Độ ưu tiên</label>
                <p class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $ticket->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $ticket->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </p>
            </div>

            <div>
                <label class="text-sm text-gray-600">Trạng thái</label>
                <p class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </p>
            </div>

            <div>
                <label class="text-sm text-gray-600">Người xử lý</label>
                <p class="mt-1 font-medium">{{ $ticket->assignedTo->name ?? 'Chưa phân công' }}</p>
            </div>
        </div>

        <div class="border-t pt-4">
            <label class="text-sm font-medium text-gray-700">Dự án</label>
            <p class="mt-1 text-gray-900">{{ $ticket->project->name }} ({{ $ticket->project->code }})</p>
        </div>

        <div class="border-t pt-4 mt-4">
            <label class="text-sm font-medium text-gray-700">Mô tả</label>
            <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $ticket->description }}</p>
        </div>

        @if($ticket->resolution)
        <div class="border-t pt-4 mt-4">
            <label class="text-sm font-medium text-gray-700">Giải pháp</label>
            <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $ticket->resolution }}</p>
        </div>
        @endif

        <div class="border-t pt-4 mt-4 grid grid-cols-3 gap-4 text-sm">
            <div>
                <label class="text-gray-600">Người tạo</label>
                <p class="font-medium">{{ $ticket->creator->name }}</p>
            </div>
            <div>
                <label class="text-gray-600">Ngày tạo</label>
                <p class="font-medium">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if($ticket->resolved_at)
            <div>
                <label class="text-gray-600">Ngày giải quyết</label>
                <p class="font-medium">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
