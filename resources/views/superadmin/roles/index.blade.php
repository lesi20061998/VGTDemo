@extends('superadmin.layouts.app')
@section('title', 'Quản lý Roles | Super Admin')
@section('page-title', 'Quản lý Roles (Vai trò)')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Danh sách Vai trò (Roles)</h1>
            <p class="text-gray-500 mt-1">Quản lý các nhóm quyền hạn trong hệ thống</p>
        </div>
        <div>
            <a href="{{ route('superadmin.roles.create') }}" class="px-6 py-3 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium inline-flex items-center transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm Vai trò
            </a>
        </div>
    </div>

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
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Tên Vai trò</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 w-1/2">Quyền hạn (Permissions)</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-medium text-gray-900">{{ $role->display_name }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $role->name }}</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($role->permissions as $perm)
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded text-[11px] font-medium" title="{{ $perm->name }}">
                                        {{ $perm->display_name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 text-xs italic">Chưa cấp quyền</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('superadmin.roles.edit', $role->id) }}" class="p-2 text-[#001B4E] bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors" title="Cấu hình">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </a>
                                @if(!in_array($role->name, ['account', 'dev', 'superadmin']))
                                <form action="{{ route('superadmin.roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Role này?');">
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
                        <td colspan="3" class="py-8 text-center text-gray-500">Chưa có dữ liệu Vai trò nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection