@extends('superadmin.layouts.app')

@section('title', 'Quản lý Nhân sự')
@section('page-title', 'Quản lý Nhân sự')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Danh sách Nhân sự</h1>
        <a href="{{ route('superadmin.employees.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tạo Nhân sự
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Mã NS</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Họ tên</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Vị trí</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Chức danh</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Bộ phận</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Quản lý</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Hợp đồng</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Trạng thái</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($employees as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><span class="font-mono font-bold text-purple-600">{{ $employee->code }}</span></td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $employee->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $employee->position === 'manager' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $employee->position === 'team_lead' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $employee->position === 'staff' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ $employee->position === 'manager' ? 'Quản lý' : ($employee->position === 'team_lead' ? 'Trưởng nhóm' : 'Nhân viên') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->role ?? '-' }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $employee->department == 'dev' ? 'bg-blue-100 text-blue-800' : ($employee->department == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">{{ strtoupper($employee->department) }}</span></td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->manager?->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->contracts_count }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $employee->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('superadmin.employees.edit', $employee) }}" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('superadmin.employees.destroy', $employee) }}" 
                                  onsubmit="return confirm('Xóa nhân sự này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
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
                    <td colspan="10" class="px-6 py-12 text-center text-gray-500">Chưa có nhân sự nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
