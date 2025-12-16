@extends('superadmin.layouts.app')

@section('title', 'Quản lý Tenants')
@section('page-title', 'Multi-Tenant Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold">Quản lý Tenants</h2>
    <a href="{{ route('superadmin.tenants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        Tạo Tenant Mới
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dữ liệu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($tenants as $tenant)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $tenant->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">{{ $tenant->code }}</span>
                </td>
                <td class="px-6 py-4">
                    <a href="http://{{ $tenant->domain }}" target="_blank" class="text-blue-600 hover:underline">
                        {{ $tenant->domain }}
                    </a>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-sm
                        {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $tenant->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($tenant->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    <div class="space-y-1">
                        <div>Users: {{ $tenant->users_count ?? 0 }}</div>
                        <div>Products: {{ $tenant->products_count ?? 0 }}</div>
                        <div>Posts: {{ $tenant->posts_count ?? 0 }}</div>
                        <div>Orders: {{ $tenant->orders_count ?? 0 }}</div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex space-x-2">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" 
                           class="text-blue-600 hover:text-blue-800">Xem</a>
                        <button onclick="controlWebsite('{{ $tenant->id }}', 'status')" 
                                class="text-purple-600 hover:text-purple-800">Kiểm tra</button>
                        <button onclick="syncData('{{ $tenant->id }}')" 
                                class="text-orange-600 hover:text-orange-800">Đồng bộ</button>
                        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" 
                           class="text-green-600 hover:text-green-800">Sửa</a>
                        @if($tenant->code !== 'default')
                        <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}" 
                              class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa tenant này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Xóa</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($tenants->isEmpty())
<div class="text-center py-12">
    <p class="text-gray-500">Chưa có tenant nào</p>
</div>
@endif

<script>
function controlWebsite(tenantId, action) {
    fetch(`/superadmin/websites/${tenantId}/control?action=${action}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kết nối thành công: ' + JSON.stringify(data.data));
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => alert('Lỗi kết nối: ' + error));
}

function syncData(tenantId) {
    fetch(`/superadmin/websites/${tenantId}/sync`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Lỗi đồng bộ: ' + data.message);
        }
    });
}
</script>
@endsection