@extends('superadmin.layouts.app')
@section('title', 'Quản lý Quyền | Super Admin')
@section('page-title', 'Quản lý Quyền (Permissions)')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Danh sách Quyền (Permissions)</h1>
            <p class="text-gray-500 mt-1">Định nghĩa các chức năng mà Role có thể thao tác</p>
        </div>
        <div>
            <a href="{{ route('superadmin.permissions.create') }}" class="px-6 py-3 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium inline-flex items-center transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm Quyền
            </a>
        </div>
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
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Tên Quyền (Mã)</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Tên Hiển Thị</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Nhóm (Group)</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($permissions as $perm)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-medium text-[#001B4E] font-mono text-sm">{{ $perm->name }}</div>
                        </td>
                        <td class="py-4 px-6 text-gray-700 font-medium">{{ $perm->display_name }}</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                                {{ $perm->group ?: 'Chung' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('superadmin.permissions.edit', $perm->id) }}" class="p-2 text-[#001B4E] bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors" title="Chỉnh sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('superadmin.permissions.destroy', $perm->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa quyền này? Sẽ ảnh hưởng đến Role đang sử dụng nó.');">
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
                        <td colspan="4" class="py-8 text-center text-gray-500">Chưa có dữ liệu Quyền nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($permissions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $permissions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection