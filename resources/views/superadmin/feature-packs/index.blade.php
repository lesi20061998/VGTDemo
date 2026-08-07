@extends('superadmin.layouts.app')
@section('title', 'Quản lý Feature Packs | Super Admin')
@section('page-title', 'Gói Tính Năng (Feature Packs)')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Danh sách Feature Packs</h1>
            <p class="text-gray-500 mt-1">Quản lý các tính năng mở rộng có thể cài đặt vào website</p>
        </div>
        <div>
            <a href="{{ route('superadmin.feature-packs.create') }}" class="px-6 py-3 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium inline-flex items-center transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm Feature Pack
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Tên Tính Năng</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Mã (Code)</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600">Nhóm</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 text-center">Trạng thái</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($featurePacks as $pack)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-medium text-gray-900">{{ $pack->name }}</div>
                            @if($pack->description)
                            <div class="text-xs text-gray-500 mt-1">{{ $pack->description }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-mono text-sm text-blue-600">{{ $pack->code }}</td>
                        <td class="py-4 px-6">
                            @if($pack->group_name)
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">{{ $pack->group_name }}</span>
                            @else
                            <span class="text-gray-400 italic text-xs">Không có nhóm</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($pack->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Hoạt động</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Tạm khóa</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('superadmin.feature-packs.edit', $pack->id) }}" class="p-2 text-[#001B4E] bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors" title="Sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('superadmin.feature-packs.destroy', $pack->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Feature Pack này?');">
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
                        <td colspan="5" class="py-8 text-center text-gray-500">Chưa có Feature Pack nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
