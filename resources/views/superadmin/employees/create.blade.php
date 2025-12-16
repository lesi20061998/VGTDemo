@extends('superadmin.layouts.app')

@section('title', 'Tạo Nhân sự')
@section('page-title', 'Tạo Nhân sự Mới')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('superadmin.employees.store') }}" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mã Nhân sự *</label>
                <input type="text" name="code" value="{{ old('code') }}" required 
                       placeholder="sivgt, abc123"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên *</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí *</label>
                <select name="position" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Chọn vị trí --</option>
                    <option value="Nhân viên" {{ old('position') == 'Nhân viên' ? 'selected' : '' }}>Nhân viên</option>
                    <option value="Trưởng nhóm" {{ old('position') == 'Trưởng nhóm' ? 'selected' : '' }}>Trưởng nhóm</option>
                    <option value="Giám đốc" {{ old('position') == 'Giám đốc' ? 'selected' : '' }}>Giám đốc</option>
                </select>
                @error('position')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bộ phận *</label>
                @if($isDepartmentManager)
                    <input type="hidden" name="department" value="{{ $userDepartment }}">
                    <input type="text" value="{{ ucfirst(str_replace('_', ' ', $userDepartment)) }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                @else
                <select name="department" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Chọn bộ phận --</option>
                    <option value="truyen_thong" {{ old('department') == 'truyen_thong' ? 'selected' : '' }}>Bộ Phận Truyền Thông</option>
                    <option value="ky_thuat" {{ old('department') == 'ky_thuat' ? 'selected' : '' }}>Bộ Phận Kỹ Thuật</option>
                    <option value="thiet_ke" {{ old('department') == 'thiet_ke' ? 'selected' : '' }}>Bộ Phận Thiết Kế</option>
                    <option value="hanh_chanh" {{ old('department') == 'hanh_chanh' ? 'selected' : '' }}>Bộ Phận Hành Chánh Nhân Sự</option>
                    <option value="ke_toan" {{ old('department') == 'ke_toan' ? 'selected' : '' }}>Kế Toán</option>
                    <option value="kinh_doanh" {{ old('department') == 'kinh_doanh' ? 'selected' : '' }}>Bộ Phận Kinh Doanh</option>
                </select>
                @endif
                @error('department')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            @if(!$isDepartmentManager)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role SuperAdmin *</label>
                <select name="superadmin_role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Chọn role --</option>
                    <option value="superadmin" {{ old('superadmin_role') == 'superadmin' ? 'selected' : '' }}>SuperAdmin (Full quyền)</option>
                    <option value="director" {{ old('superadmin_role') == 'director' ? 'selected' : '' }}>Director (Giám đốc vận hành)</option>
                    <option value="account" {{ old('superadmin_role') == 'account' ? 'selected' : '' }}>Account (Kinh doanh)</option>
                    <option value="dev" {{ old('superadmin_role') == 'dev' ? 'selected' : '' }}>Dev (Kỹ thuật)</option>
                </select>
                @error('superadmin_role')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_department_manager" value="1" 
                           {{ old('is_department_manager') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-600">
                    <span class="ml-2 text-sm text-gray-700">Quản lý bộ phận (mỗi bộ phận chỉ có 1 quản lý)</span>
                </label>
                @error('is_department_manager')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            @else
            <input type="hidden" name="superadmin_role" value="account">
            <input type="hidden" name="is_department_manager" value="0">
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role trong Bộ phận</label>
                <select name="department_role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Chọn role --</option>
                    <option value="truong_phong" {{ old('department_role') == 'truong_phong' ? 'selected' : '' }}>Trưởng phòng</option>
                    <option value="pho_phong" {{ old('department_role') == 'pho_phong' ? 'selected' : '' }}>Phó phòng</option>
                    <option value="nhan_vien" {{ old('department_role') == 'nhan_vien' ? 'selected' : '' }}>Nhân viên</option>
                </select>
                @error('department_role')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quản lý trực tiếp</label>
                <select name="manager_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    <option value="">-- Không có --</option>
                    @foreach(\App\Models\Employee::where('is_active', true)->get() as $emp)
                    <option value="{{ $emp->id }}" {{ old('manager_id') == $emp->id ? 'selected' : '' }}>
                        [{{ $emp->code }}] {{ $emp->name }} - {{ strtoupper($emp->department) }}
                    </option>
                    @endforeach
                </select>
                @error('manager_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu tài khoản *</label>
                <input type="password" name="password" required 
                       placeholder="Tối thiểu 6 ký tự"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
            <a href="{{ route('superadmin.employees.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
            <button type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Tạo Nhân sự</button>
        </div>
    </form>
</div>
@endsection
