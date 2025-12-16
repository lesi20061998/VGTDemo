@extends('cms.layouts.app')

@section('title', 'Form Submissions')
@section('page-title', 'Quản lý Form Submissions')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b">
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">Danh sách submissions</h3>
            <div class="flex gap-2">
                <a href="?status=pending" class="px-3 py-1 text-sm border rounded">Chờ duyệt</a>
                <a href="?status=approved" class="px-3 py-1 text-sm border rounded">Đã duyệt</a>
            </div>
        </div>
    </div>
    
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-sm">Form</th>
                <th class="px-6 py-3 text-left text-sm">Dữ liệu</th>
                <th class="px-6 py-3 text-left text-sm">IP</th>
                <th class="px-6 py-3 text-left text-sm">Trạng thái</th>
                <th class="px-6 py-3 text-left text-sm">Ngày</th>
                <th class="px-6 py-3 text-right text-sm">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $submission)
            <tr class="border-t">
                <td class="px-6 py-4 text-sm">{{ $submission->form_name }}</td>
                <td class="px-6 py-4 text-sm">
                    @foreach($submission->data as $key => $value)
                    <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                    @endforeach
                </td>
                <td class="px-6 py-4 text-sm">{{ $submission->ip_address }}</td>
                <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 rounded text-xs {{ $submission->status === 'pending' ? 'bg-yellow-100' : 'bg-green-100' }}">
                        {{ ucfirst($submission->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-4 text-sm text-right">
                    <form method="POST" action="{{ route('cms.form-submissions.update-status', $submission->id) }}" class="inline">
                        @csrf
                        <select name="status" onchange="this.form.submit()" class="text-sm border rounded px-2 py-1">
                            <option value="pending" {{ $submission->status === 'pending' ? 'selected' : '' }}>Chờ</option>
                            <option value="approved" {{ $submission->status === 'approved' ? 'selected' : '' }}>Duyệt</option>
                            <option value="rejected" {{ $submission->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </form>
                    <form method="POST" action="{{ route('cms.form-submissions.destroy', $submission->id) }}" class="inline ml-2">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Xóa?')" class="text-red-600">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="p-6">{{ $submissions->links() }}</div>
</div>
@endsection
