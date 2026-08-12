@extends('cms.layouts.app')

@section('title', 'Form Liên hệ')
@section('page-title', 'Quản lý Form liên hệ')

@section('content')
@php $projectCode = request()->route('projectCode'); @endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Form liên hệ</h1>
                <p class="text-gray-600 text-sm mt-1">Danh sách thông tin khách hàng đã gửi form</p>
            </div>
        </div>

        <form method="GET" class="mt-4 flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email, nội dung..."
                   class="px-4 py-2 border border-gray-300 rounded-lg flex-1 min-w-48">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã xử lý</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
            @if($formNames->count() > 1)
            <select name="form_name" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả form</option>
                @foreach($formNames as $fname)
                    <option value="{{ $fname }}" {{ request('form_name') === $fname ? 'selected' : '' }}>{{ $fname }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg">Lọc</button>
            @if(request()->anyFilled(['search','status','form_name']))
                <a href="{{ url()->current() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600">Xóa lọc</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Form</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thông tin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày gửi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                {{ $submission->form_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm max-w-sm">
                            @foreach(array_filter((array)$submission->data) as $key => $value)
                                <div class="text-xs text-gray-500">
                                    <span class="font-medium text-gray-700">{{ ucfirst($key) }}:</span>
                                    {{ Str::limit($value, 80) }}
                                </div>
                            @endforeach
                            @if($submission->admin_note)
                                <div class="mt-1 text-xs text-purple-600 italic">Ghi chú: {{ $submission->admin_note }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $submission->ip_address }}</td>
                        <td class="px-6 py-4">
                            @php
                                $sc = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                                $sl = ['pending' => 'Chờ xử lý', 'approved' => 'Đã xử lý', 'rejected' => 'Từ chối'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $sc[$submission->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $sl[$submission->status] ?? $submission->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $submission->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST"
                                      action="{{ route('project.admin.form-submissions.update-status', [$projectCode, $submission->id]) }}"
                                      class="inline flex items-center gap-1">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="text-xs border rounded px-2 py-1">
                                        <option value="pending"  {{ $submission->status === 'pending'  ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="approved" {{ $submission->status === 'approved' ? 'selected' : '' }}>Đã xử lý</option>
                                        <option value="rejected" {{ $submission->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                    </select>
                                </form>
                                <form method="POST"
                                      action="{{ route('project.admin.form-submissions.destroy', [$projectCode, $submission->id]) }}"
                                      class="inline"
                                      onsubmit="return confirm('Xóa submission này?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900 text-xs">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <p>Chưa có submission nào.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="p-6 border-t">{{ $submissions->links() }}</div>
        @endif
    </div>
</div>
@endsection


