@extends('superadmin.layouts.app')

@section('page-title', 'Quản lý Tickets')

@section('content')
<div class="px-6 py-6 max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Tất cả Tickets</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">ID</th>
                        <th class="p-4">Dự án</th>
                        <th class="p-4">Tiêu đề</th>
                        <th class="p-4">Người tạo</th>
                        <th class="p-4">Trạng thái</th>
                        <th class="p-4">Cập nhật</th>
                        <th class="p-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm font-medium text-gray-900">#{{ $ticket->id }}</td>
                        <td class="p-4 text-sm text-gray-600">
                            @if($ticket->project)
                                <a href="{{ route('superadmin.projects.show', $ticket->project->id) }}" class="text-[#002D80] hover:underline font-medium">
                                    {{ $ticket->project->name }}
                                </a>
                            @else
                                <span class="text-gray-400 italic">Dự án đã xóa</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="text-sm font-medium text-gray-900 line-clamp-1">{{ $ticket->title }}</div>
                        </td>
                        <td class="p-4">
                            <div class="text-sm text-gray-900 font-medium">{{ $ticket->creator->name ?? 'Người dùng' }}</div>
                            <div class="text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td class="p-4">
                            @if($ticket->status === 'open')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Mới</span>
                            @elseif($ticket->status === 'processing')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Đang xử lý</span>
                            @elseif($ticket->status === 'replying')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Đã phản hồi</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Đóng</span>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-gray-500">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </td>
                        <td class="p-4 text-sm font-medium">
                            @if($ticket->project)
                                <a href="{{ route('superadmin.projects.show', $ticket->project->id) }}" class="inline-flex items-center text-sm font-medium text-[#002D80] hover:text-blue-900">
                                    Xem chi tiết 
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            @else
                                <span class="text-gray-400 cursor-not-allowed">Không khả dụng</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p class="text-base font-medium">Chưa có ticket nào</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tickets->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
