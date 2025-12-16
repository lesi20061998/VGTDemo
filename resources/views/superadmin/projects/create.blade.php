@extends('superadmin.layouts.app')

@section('title', 'Tạo Dự án')
@section('page-title', 'Tạo Dự án từ Hợp đồng')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('superadmin.projects.store') }}" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hợp đồng đã duyệt *</label>
                <select name="contract_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Chọn hợp đồng --</option>
                    @foreach($contracts as $contract)
                    <option value="{{ $contract->id }}">
                        {{ $contract->full_code }} - {{ $contract->client_name }} ({{ $contract->employee->name }})
                    </option>
                    @endforeach
                </select>
                @error('contract_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mã Dự án *</label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="PRJ001"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên Dự án *</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên Khách hàng *</label>
                <input type="text" name="client_name" value="{{ old('client_name') }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('client_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nhân sự phụ trách *</label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Chọn nhân sự --</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">
                        [{{ $employee->code }}] {{ $employee->name }} - {{ strtoupper($employee->department) }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Subdomain sẽ tự động tạo: {mã_nhân_sự}.vnglobaltech.com/{mã_hợp_đồng}</p>
                @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                <textarea name="notes" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
            <a href="{{ route('superadmin.projects.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
            <button type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Tạo & Phân phối Dự án</button>
        </div>
    </form>
</div>
@endsection
