@extends('superadmin.layouts.app')
@section('title', 'Quản lý Tenants | Super Admin')
@section('page-title', 'Multi-Tenant Management')

@section('content')
<div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#001B4E]">Quản lý Tenants</h1>
            <p class="text-gray-500 mt-1">Quản lý và giám sát các website vệ tinh (Tenants) trong hệ thống</p>
        </div>
        <div>
            <a href="{{ route('superadmin.tenants.create') }}" class="px-6 py-3 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium inline-flex items-center transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo Tenant Mới
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
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Tên Tenant</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Domain</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider">Dữ liệu</th>
                        <th class="py-4 px-6 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-medium text-[#001B4E]">{{ $tenant->name }}</div>
                            <div class="text-xs font-mono text-gray-500 mt-1">Code: {{ $tenant->code }}</div>
                        </td>
                        <td class="py-4 px-6">
                            <a href="http://{{ $tenant->domain }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $tenant->domain }}
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $tenant->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
                                <div><span class="font-medium text-gray-800">{{ $tenant->users_count ?? 0 }}</span> Users</div>
                                <div><span class="font-medium text-gray-800">{{ $tenant->products_count ?? 0 }}</span> Products</div>
                                <div><span class="font-medium text-gray-800">{{ $tenant->posts_count ?? 0 }}</span> Posts</div>
                                <div><span class="font-medium text-gray-800">{{ $tenant->orders_count ?? 0 }}</span> Orders</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="p-2 text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors" title="Xem chi tiết">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <button onclick="controlWebsite('{{ $tenant->id }}', 'status')" class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors" title="Kiểm tra kết nối">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </button>
                                
                                <button onclick="syncData('{{ $tenant->id }}')" class="p-2 text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors" title="Đồng bộ dữ liệu">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                                
                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="p-2 text-[#001B4E] bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors" title="Chỉnh sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                @if($tenant->code !== 'default')
                                <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Tenant này? Toàn bộ dữ liệu của website sẽ bị mất.');">
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
                        <td colspan="5" class="py-8 text-center text-gray-500">Chưa có Tenant nào được tạo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($tenants, 'hasPages') && $tenants->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function controlWebsite(tenantId, action) {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    btn.disabled = true;

    fetch(`/superadmin/websites/${tenantId}/control?action=${action}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        if (data.success) {
            alert('Kết nối thành công: ' + JSON.stringify(data.data));
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        alert('Lỗi kết nối: ' + error);
    });
}

function syncData(tenantId) {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    btn.disabled = true;

    fetch(`/superadmin/websites/${tenantId}/sync`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        if (data.success) {
            alert('Thành công: ' + data.message);
        } else {
            alert('Lỗi đồng bộ: ' + data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        alert('Lỗi kết nối: ' + error);
    });
}
</script>
@endpush
@endsection
