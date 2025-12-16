@extends('superadmin.layouts.app')

@section('title', 'Sửa Dự án')
@section('page-title', 'Chỉnh sửa Dự án')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('superadmin.projects.update', $project) }}" class="bg-white rounded-lg shadow-sm p-6">
        @csrf @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên Dự án *</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subdomain *</label>
                <input type="text" name="subdomain" value="{{ old('subdomain', $project->subdomain) }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                       placeholder="localhost/project-code hoặc employee.vnglobaltech.com/contract-code">
                @error('subdomain')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                <p class="text-sm text-gray-500 mt-1">Ví dụ: localhost/HD01 (local) hoặc sivgt.vnglobaltech.com/HD01 (production)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nhân sự phụ trách *</label>
                <select name="employee_ids[]" multiple required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600" size="5">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" 
                        {{ in_array($employee->id, $project->employee_ids ?? []) ? 'selected' : '' }}>
                        {{ $employee->name }} - {{ ucfirst($employee->position ?? 'staff') }}
                    </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều nhân sự</p>
                @error('employee_ids')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                <textarea name="notes" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">{{ old('notes', $project->notes) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
            <a href="{{ route('superadmin.projects.show', $project) }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
            <button type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Cập nhật</button>
        </div>
    </form>
</div>
@endsection
