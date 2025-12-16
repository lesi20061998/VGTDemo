@extends('superadmin.layouts.app')

@section('title', 'Tạo Task mới')
@section('page-title', 'Tạo Task mới')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('superadmin.tasks.store') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dự án</label>
                    <select name="project_id" required class="w-full border rounded-lg px-4 py-2">
                        <option value="">Chọn dự án</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Người thực hiện</label>
                    <select name="assigned_to" required class="w-full border rounded-lg px-4 py-2">
                        <option value="">Chọn nhân viên</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề</label>
                    <input type="text" name="title" required class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                    <textarea name="description" rows="4" class="w-full border rounded-lg px-4 py-2"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ưu tiên</label>
                        <select name="priority" required class="w-full border rounded-lg px-4 py-2">
                            <option value="low">Thấp</option>
                            <option value="medium" selected>Trung bình</option>
                            <option value="high">Cao</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hạn hoàn thành</label>
                        <input type="date" name="due_date" class="w-full border rounded-lg px-4 py-2">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('superadmin.tasks.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Tạo Task</button>
            </div>
        </form>
    </div>
</div>
@endsection
