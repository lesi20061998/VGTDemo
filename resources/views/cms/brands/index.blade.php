{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý nhà sản xuất')
@section('page-title', 'Nhà sản xuất')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Tìm kiếm nhà sản xuất..." 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Tìm kiếm
        </button>
    </form>
    
    <a href="{{ route('project.admin.brands.create', request()->route('projectCode')) }}" 
       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
        Thêm nhà sản xuất
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($brands as $brand)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($brand->logo)
                            <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="h-8 w-8 rounded-full mr-3 object-cover">
                        @else
                            <div class="h-8 w-8 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                <span class="text-gray-600 text-xs font-bold">{{ substr($brand->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="text-sm font-medium text-gray-900">{{ $brand->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $brand->slug }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ $brand->products()->count() }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $brand->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $brand->is_active ? 'Kích hoạt' : 'Vô hiệu' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('project.admin.brands.edit', [request()->route('projectCode'), $brand]) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        <form method="POST" action="{{ route('project.admin.brands.destroy', [request()->route('projectCode'), $brand]) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Không có nhà sản xuất nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $brands->links() }}
</div>
@endsection
