@extends('superadmin.layouts.app')

@section('title', 'Tạo Tenant Mới')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Tạo Website Mới</h2>
        <p class="text-gray-600">Tạo một tenant mới với dữ liệu riêng biệt</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('superadmin.tenants.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên Website</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ví dụ: Cửa hàng ABC" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="abc-store" required>
                    <p class="text-gray-500 text-sm mt-1">Chỉ sử dụng chữ thường, số và dấu gạch ngang</p>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Domain</label>
                    <input type="text" name="domain" value="{{ old('domain') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="abc-store.local" required>
                    <p class="text-gray-500 text-sm mt-1">Domain hoặc subdomain cho website này</p>
                    @error('domain')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Database</label>
                    <input type="text" name="database_name" value="{{ old('database_name', 'agency_cms') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    <p class="text-gray-500 text-sm mt-1">Tên database (hiện tại dùng chung agency_cms)</p>
                    @error('database_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tùy chọn Website</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="create_website" id="create_website" value="1" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="create_website" class="ml-2 block text-sm text-gray-900">
                                Tự động tạo website với database riêng và export source code
                            </label>
                        </div>
                        
                        <div id="export_options" class="ml-6 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Đường dẫn export (tùy chọn)</label>
                            <input type="text" name="export_path" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="c:\xampp\htdocs\ten-website">
                            <p class="text-gray-500 text-sm mt-1">Để trống sẽ tự động tạo theo tên code</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('superadmin.tenants.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Tạo Tenant
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('create_website').addEventListener('change', function() {
    const exportOptions = document.getElementById('export_options');
    if (this.checked) {
        exportOptions.classList.remove('hidden');
    } else {
        exportOptions.classList.add('hidden');
    }
});
</script>
@endsection