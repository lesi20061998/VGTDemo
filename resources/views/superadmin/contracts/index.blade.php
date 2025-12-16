@extends('superadmin.layouts.app')

@section('title', 'Quản lý Hợp đồng')
@section('page-title', 'Quản lý Hợp đồng')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Danh sách Hợp đồng</h1>
        <a href="{{ route('superadmin.contracts.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tạo Hợp đồng
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Mã HĐ</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Khách hàng</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Account</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Deadline</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Trạng thái</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($contracts as $contract)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><span class="font-mono font-bold text-purple-600">{{ $contract->full_code }}</span></td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $contract->client_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $contract->employee->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $contract->deadline?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium 
                            {{ $contract->status == 'approved' ? 'bg-green-100 text-green-800' : 
                               ($contract->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($contract->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            @if($contract->status == 'pending')
                            <form method="POST" action="{{ route('superadmin.contracts.approve', $contract) }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Duyệt">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('superadmin.contracts.reject', $contract) }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Từ chối">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('superadmin.contracts.show', $contract) }}" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Xem">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('superadmin.contracts.destroy', $contract) }}" 
                                  onsubmit="return confirm('Xóa hợp đồng này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có hợp đồng nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
