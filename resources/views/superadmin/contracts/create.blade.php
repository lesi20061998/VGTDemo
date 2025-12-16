@extends('superadmin.layouts.app')

@section('title', 'Tạo Hợp đồng')
@section('page-title', 'Tạo Hợp đồng Mới')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('superadmin.contracts.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Account (Nhân sự) *</label>
                <select name="employee_id" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Chọn Account --</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">
                        [{{ $employee->code }}] {{ $employee->name }}
                    </option>
                    @endforeach
                </select>
                @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mã Hợp đồng *</label>
                <input type="text" name="contract_code" value="{{ old('contract_code') }}" required 
                       placeholder="HD01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('contract_code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên Khách hàng *</label>
                <input type="text" name="client_name" value="{{ old('client_name') }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('client_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loại Dịch vụ</label>
                <input type="text" name="service_type" value="{{ old('service_type') }}" 
                       placeholder="Website, App, Landing Page..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('service_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Yêu cầu Nghiệp vụ</label>
                <textarea name="requirements" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">{{ old('requirements') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả Thiết kế</label>
                <textarea name="design_description" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">{{ old('design_description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">File đính kèm</label>
                <input type="file" name="attachments" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('attachments')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu *</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required 
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600" onchange="calculateDuration(); updateDeadlineMin()">
                    @error('start_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deadline *</label>
                    <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" required 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600" onchange="calculateDuration()">
                    @error('deadline')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div id="duration-info" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-800">Thời gian dự án: <span id="duration-text"></span></span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú nội bộ</label>
                <textarea name="notes" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
            <a href="{{ route('superadmin.contracts.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
            <button type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Gửi Hợp đồng</button>
        </div>
    </form>
</div>

<script>
function calculateDuration() {
    const startDate = document.getElementById('start_date').value;
    const deadline = document.getElementById('deadline').value;
    const durationInfo = document.getElementById('duration-info');
    const durationText = document.getElementById('duration-text');
    
    if (startDate && deadline) {
        const start = new Date(startDate);
        const end = new Date(deadline);
        
        if (end > start) {
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const months = Math.floor(diffDays / 30);
            const days = diffDays % 30;
            
            let durationStr = '';
            if (months > 0) {
                durationStr += months + ' tháng ';
            }
            if (days > 0) {
                durationStr += days + ' ngày';
            }
            
            durationText.textContent = durationStr + ' (' + diffDays + ' ngày)';
            durationInfo.classList.remove('hidden');
        } else {
            durationInfo.classList.add('hidden');
        }
    } else {
        durationInfo.classList.add('hidden');
    }
}

function updateDeadlineMin() {
    const startDate = document.getElementById('start_date').value;
    const deadlineInput = document.getElementById('deadline');
    
    if (startDate) {
        const nextDay = new Date(startDate);
        nextDay.setDate(nextDay.getDate() + 1);
        deadlineInput.min = nextDay.toISOString().split('T')[0];
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateDuration();
    updateDeadlineMin();
});
</script>
@endsection
