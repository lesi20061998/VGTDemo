@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Chi tiết người dùng')
@section('page-title', 'Chi tiết người dùng')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Chi tiết người dùng: {{ $user->name }}</h2>
        <div class="flex items-center gap-3">
            <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.users.edit', $user) : route('project.admin.users.edit', ['projectCode' => request()->route('projectCode'), 'user' => $user]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                Sửa người dùng
            </a>
            <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.users.index') : route('project.admin.users.index', request()->route('projectCode')) }}" class="text-gray-500 hover:text-gray-700">
                &larr; Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Tên hiển thị</h3>
            <p class="mt-1 text-base text-gray-900">{{ $user->name }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Email</h3>
            <p class="mt-1 text-base text-gray-900">{{ $user->email }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Username</h3>
            <p class="mt-1 text-base text-gray-900">{{ $user->username ?? 'N/A' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Số điện thoại</h3>
            <p class="mt-1 text-base text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Địa chỉ</h3>
            <p class="mt-1 text-base text-gray-900">{{ $user->address ?? 'N/A' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Trạng thái</h3>
            <p class="mt-1 text-base">
                @if($user->status)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Hoạt động</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Khóa</span>
                @endif
            </p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500">Vai trò</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($user->roles as $role)
                    <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $role->display_name ?? $role->name }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
