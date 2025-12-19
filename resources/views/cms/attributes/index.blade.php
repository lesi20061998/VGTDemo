{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý thuộc tính')
@section('page-title', 'Thuộc tính sản phẩm')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Tìm kiếm thuộc tính..." 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        
        <select name="group_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả nhóm</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
        
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Tìm kiếm
        </button>
    </form>

    <div class="flex gap-2">
        <a href="{{ route('project.admin.attributes.groups.index', request()->route('projectCode')) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Quản lý nhóm
        </a>
        <a href="{{ route('project.admin.attributes.create', request()->route('projectCode')) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Thêm thuộc tính
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhóm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá trị</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($attributes as $attribute)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $attribute->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attribute->group?->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $attribute->type }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('project.admin.attributes.show', [request()->route('projectCode'), $attribute]) }}" 
                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                        {{ $attribute->values()->count() }} giá trị
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('project.admin.attributes.edit', [request()->route('projectCode'), $attribute]) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        <form method="POST" action="{{ route('project.admin.attributes.destroy', [request()->route('projectCode'), $attribute]) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Không có thuộc tính nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $attributes->links() }}
</div>
@endsection
