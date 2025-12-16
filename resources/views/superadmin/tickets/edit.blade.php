@extends('superadmin.layouts.app')

@section('title', 'Cập nhật Ticket')
@section('page-title', 'Cập nhật Ticket')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('superadmin.tickets.update', $ticket) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã Ticket</label>
                    <input type="text" value="{{ $ticket->ticket_number }}" disabled class="w-full border rounded-lg px-4 py-2 bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                    <input type="text" name="title" value="{{ $ticket->title }}" required class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả chi tiết *</label>
                    <textarea name="description" rows="6" required class="w-full border rounded-lg px-4 py-2">{{ $ticket->description }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái *</label>
                        <select name="status" required class="w-full border rounded-lg px-4 py-2">
                            <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Mở</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                            <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Đóng</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Độ ưu tiên *</label>
                        <select name="priority" required class="w-full border rounded-lg px-4 py-2">
                            <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Thấp</option>
                            <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>Cao</option>
                            <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phân công</label>
                    <select name="assigned_to" class="w-full border rounded-lg px-4 py-2">
                        <option value="">Chưa phân công</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $ticket->assigned_to == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giải pháp / Kết quả</label>
                    <textarea name="resolution" rows="4" class="w-full border rounded-lg px-4 py-2" placeholder="Nhập giải pháp hoặc kết quả xử lý">{{ $ticket->resolution }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('superadmin.tickets.show', $ticket) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Cập nhật</button>
            </div>
        </form>
    </div>
</div>
@endsection
