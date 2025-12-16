@extends('superadmin.layouts.app')

@section('title', 'Sửa Hợp đồng')
@section('page-title', 'Chỉnh sửa Hợp đồng')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('superadmin.contracts.update', $contract) }}" class="bg-white rounded-lg shadow-sm p-6">
        @csrf @method('PUT')
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nhân sự *</label>
                <select name="employee_id" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id', $contract->employee_id) == $employee->id ? 'selected' : '' }}>
                        [{{ $employee->code }}] {{ $employee->name }}
                    </option>
                    @endforeach
                </select>
                @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Website *</label>
                <select name="website_id" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    @foreach($websites as $website)
                    <option value="{{ $website->id }}" {{ old('website_id', $contract->website_id) == $website->id ? 'selected' : '' }}>
                        {{ $website->slug }}
                    </option>
                    @endforeach
                </select>
                @error('website_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mã Hợp đồng *</label>
                <input type="text" name="contract_code" value="{{ old('contract_code', $contract->contract_code) }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('contract_code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu *</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required 
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600" onchange="calculateDuration(); updateEndDateMin()">
                    @error('start_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $contract->end_date?->format('Y-m-d')) }}" 
                           min="{{ $contract->start_date ? $contract->start_date->addDay()->format('Y-m-d') : date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600" onchange="calculateDuration()">
                    @error('end_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                <textarea name="notes" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">{{ old('notes', $contract->notes) }}</textarea>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $contract->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-600">
                    <span class="ml-2 text-sm text-gray-700">Kích hoạt</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
            <a href="{{ route('superadmin.contracts.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
            <button type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Cập nhật</button>
        </div>
    </form>
</div>

<script>
function calculateDuration() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const durationInfo = document.getElementById('duration-info');
    const durationText = document.getElementById('duration-text');
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
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

function updateEndDateMin() {
    const startDate = document.getElementById('start_date').value;
    const endDateInput = document.getElementById('end_date');
    
    if (startDate) {
        const nextDay = new Date(startDate);
        nextDay.setDate(nextDay.getDate() + 1);
        endDateInput.min = nextDay.toISOString().split('T')[0];
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateDuration();
    updateEndDateMin();
});
</script>
@endsection
