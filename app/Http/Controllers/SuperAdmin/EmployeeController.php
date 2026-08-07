<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Employee::with('manager')->withCount('contracts');
        
        // Nếu là quản lý bộ phận, chỉ xem nhân sự trong bộ phận (không bao gồm bản thân)
        if ($user->employee && $user->employee->is_department_manager && $user->level > 1) {
            $query->where('department', $user->employee->department)
                  ->where('id', '!=', $user->employee->id);
        }
        
        $employees = $query->latest()->get();
        return view('superadmin.employees.index', compact('employees'));
    }

    public function create()
    {
        $user = auth()->user();
        $isDepartmentManager = $user->employee && $user->employee->is_department_manager && $user->level > 1;
        $userDepartment = $isDepartmentManager ? $user->employee->department : null;
        
        return view('superadmin.employees.create', compact('isDepartmentManager', 'userDepartment'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isDepartmentManager = $user->employee && $user->employee->is_department_manager && $user->level > 1;
        
        // Nếu là quản lý bộ phận, chỉ cho phép tạo trong bộ phận của mình
        if ($isDepartmentManager && $request->department !== $user->employee->department) {
            return back()->withErrors(['department' => 'Bạn chỉ có thể tạo nhân sự trong bộ phận của mình.'])->withInput();
        }
        
        $request->validate([
            'code' => 'required|string|unique:employees,code|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|in:Nhân viên,Trưởng nhóm,Giám đốc',
            'department' => 'required|in:truyen_thong,ky_thuat,thiet_ke,hanh_chanh,ke_toan,kinh_doanh',
            'superadmin_role' => 'required|in:superadmin,director,account,dev',
            'is_department_manager' => 'nullable|boolean',
            'department_role' => 'nullable|in:truong_phong,pho_phong,nhan_vien',
            'manager_id' => 'nullable|exists:employees,id',
            'password' => 'required|string|min:6',
        ]);

        // Kiểm tra nếu là quản lý bộ phận, chỉ cho phép 1 người/bộ phận
        if ($request->is_department_manager) {
            $existing = Employee::where('department', $request->department)
                ->where('is_department_manager', true)
                ->exists();
            if ($existing) {
                return back()->withErrors(['is_department_manager' => 'Bộ phận này đã có quản lý.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => in_array($request->superadmin_role, ['superadmin', 'director']) ? 1 : 2,
            'role' => 'superadmin',
        ]);

        Employee::create([
            'user_id' => $user->id,
            'code' => $request->code,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'department' => $request->department,
            'superadmin_role' => $request->superadmin_role,
            'is_department_manager' => $request->boolean('is_department_manager'),
            'department_role' => $request->department_role,
            'manager_id' => $request->manager_id,
        ]);

        return redirect()->route('superadmin.employees.index')->with('alert', [
            'type' => 'success',
            'message' => 'Tạo nhân sự và tài khoản thành công!'
        ]);
    }

    public function edit(Employee $employee)
    {
        $user = auth()->user();
        $isDepartmentManager = $user->employee && $user->employee->is_department_manager && $user->level > 1;
        
        // Nếu là quản lý bộ phận, chỉ cho phép sửa nhân viên trong bộ phận
        if ($isDepartmentManager && $employee->department !== $user->employee->department) {
            abort(403, 'Bạn chỉ có thể sửa nhân sự trong bộ phận của mình.');
        }
        
        $userDepartment = $isDepartmentManager ? $user->employee->department : null;
        
        return view('superadmin.employees.edit', compact('employee', 'isDepartmentManager', 'userDepartment'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:employees,code,' . $employee->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|in:Nhân viên,Trưởng nhóm,Giám đốc',
            'department' => 'required|in:truyen_thong,ky_thuat,thiet_ke,hanh_chanh,ke_toan,kinh_doanh',
            'superadmin_role' => 'required|in:superadmin,director,account,dev',
            'is_department_manager' => 'nullable|boolean',
            'department_role' => 'nullable|in:truong_phong,pho_phong,nhan_vien',
        ]);

        // Kiểm tra nếu là quản lý bộ phận
        if ($request->is_department_manager) {
            $existing = Employee::where('department', $request->department)
                ->where('is_department_manager', true)
                ->where('id', '!=', $employee->id)
                ->exists();
            if ($existing) {
                return back()->withErrors(['is_department_manager' => 'Bộ phận này đã có quản lý.'])->withInput();
            }
        }

        $employee->update([
            'code' => $request->code,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'department' => $request->department,
            'superadmin_role' => $request->superadmin_role,
            'is_department_manager' => $request->boolean('is_department_manager'),
            'department_role' => $request->department_role,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Cập nhật user level nếu cần
        if ($employee->user) {
            $employee->user->update([
                'level' => in_array($request->superadmin_role, ['superadmin', 'director']) ? 1 : 2,
            ]);
        }

        return redirect()->route('superadmin.employees.index')->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật nhân sự thành công!'
        ]);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('superadmin.employees.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa nhân sự thành công!'
        ]);
    }
}

