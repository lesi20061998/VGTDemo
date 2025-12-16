@extends('superadmin.layouts.app')

@section('title', 'Tạo Ticket mới')
@section('page-title', 'Tạo Ticket mới')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('superadmin.tickets.store') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dự án *</label>
                    <select name="project_id" required class="w-full border rounded-lg px-4 py-2">
                        <option value="">Chọn dự án</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                    <input type="text" name="title" required class="w-full border rounded-lg px-4 py-2" placeholder="Nhập tiêu đề ticket">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả chi tiết *</label>
                    <textarea name="description" rows="6" required class="w-full border rounded-lg px-4 py-2" placeholder="Mô tả chi tiết vấn đề hoặc feedback"></textarea>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Loại *</label>
                        <select name="type" required class="w-full border rounded-lg px-4 py-2">
                            <option value="feedback">Feedback</option>
                            <option value="support">Hỗ trợ</option>
                            <option value="bug">Lỗi</option>
                            <option value="feature">Tính năng mới</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Độ ưu tiên *</label>
                        <select name="priority" required class="w-full border rounded-lg px-4 py-2">
                            <option value="low">Thấp</option>
                            <option value="medium" selected>Trung bình</option>
                            <option value="high">Cao</option>
                            <option value="urgent">Khẩn cấp</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phân công</label>
                        <select name="assigned_to" class="w-full border rounded-lg px-4 py-2">
                            <option value="">Chưa phân công</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('superadmin.tickets.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Tạo Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
