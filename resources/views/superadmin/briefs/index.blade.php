@extends('superadmin.layouts.app')
@section('title', 'Quản lý Briefs | Super Admin')
@section('page-title', 'Quản lý Briefs (Yêu cầu)')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Danh sách Briefs</h1>
            <p class="text-gray-500 mt-1">Quản lý các yêu cầu dự án từ khách hàng</p>
        </div>
        @if(auth()->user()->hasRole('account') || auth()->user()->isSuperAdmin())
        <div>
            <a href="{{ route('superadmin.briefs.create') }}" class="px-6 py-3 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium inline-flex items-center transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo Brief mới
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Tên Brief</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Khách hàng</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Ngân sách</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Deadline</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Trạng thái</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($briefs as $brief)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-medium text-gray-900">{{ $brief->title }}</div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">{{ $brief->client_name }}</td>
                        <td class="py-4 px-6 text-gray-600 font-medium">
                            {{ $brief->budget ? number_format($brief->budget) . ' VNĐ' : 'Chưa chốt' }}
                        </td>
                        <td class="py-4 px-6 text-gray-600">
                            @if($brief->deadline)
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $brief->deadline->format('d/m/Y') }}
                                </span>
                            @else
                                Không có
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($brief->status == 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @elseif($brief->status == 'submitted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Chờ duyệt
                                </span>
                            @elseif($brief->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Đã duyệt
                                </span>
                            @elseif($brief->status == 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Từ chối
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('superadmin.briefs.edit', $brief->id) }}" class="p-2 text-[#001B4E] bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors" title="Chỉnh sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('superadmin.briefs.destroy', $brief->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Brief này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">Chưa có dữ liệu Brief nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($briefs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $briefs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection