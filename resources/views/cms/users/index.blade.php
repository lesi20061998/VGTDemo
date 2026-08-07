@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Danh sách người dùng')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tìm kiếm email, tên..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            
            <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả vai trò</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                        {{ $role->display_name ?? $role->name }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
                Lọc
            </button>
        </form>
        
        <div class="flex items-center gap-2">
            <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.users.create') : route('project.admin.users.create', ['projectCode' => request()->route('projectCode')]) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2">
                Thêm mới
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-sm font-medium text-gray-500 w-10">ID</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">Tên / Email</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">Vai trò</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">Trạng thái</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $user->id }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $role->display_name ?? $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->status)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Khóa
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @php
                                    $editUrl = request()->routeIs('superadmin.*') ? route('superadmin.users.edit', $user) : route('project.admin.users.edit', ['projectCode' => request()->route('projectCode'), 'user' => $user]);
                                    $deleteUrl = request()->routeIs('superadmin.*') ? route('superadmin.users.destroy', $user) : route('project.admin.users.destroy', ['projectCode' => request()->route('projectCode'), 'user' => $user]);
                                @endphp
                                <a href="{{ $editUrl }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Sửa">
                                    Edit
                                </a>
                                
                                <form action="{{ $deleteUrl }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                            Không tìm thấy dữ liệu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
