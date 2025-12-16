<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Website;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Contract::with(['employee']);
        
        // Account role chỉ xem hợp đồng của bản thân
        if ($user->employee && $user->employee->superadmin_role === 'account') {
            $query->where('employee_id', $user->employee->id);
        }
        // Director và superadmin xem tất cả
        
        $contracts = $query->latest()->get();
        return view('superadmin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('superadmin.contracts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_code' => 'required|string|unique:contracts,contract_code',
            'client_name' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'design_description' => 'nullable|string',
            'attachments' => 'nullable|file|max:10240',
            'deadline' => 'nullable|date',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $fullCode = $employee->code . '/' . $request->contract_code;
        
        $data = [
            'employee_id' => $request->employee_id,
            'contract_code' => $request->contract_code,
            'full_code' => $fullCode,
            'start_date' => now(),
            'end_date' => $request->deadline,
            'notes' => $request->notes,
            'is_active' => true,
        ];

        Contract::create($data);

        return redirect()->route('superadmin.contracts.index')->with('alert', [
            'type' => 'success',
            'message' => 'Tạo hợp đồng thành công! Chờ Super Admin duyệt.'
        ]);
    }

    public function show(Contract $contract)
    {
        $contract->load('employee');
        return view('superadmin.contracts.show', compact('contract'));
    }

    public function approve(Contract $contract)
    {
        $contract->update(['status' => 'approved']);
        
        return back()->with('alert', [
            'type' => 'success',
            'message' => 'Duyệt hợp đồng thành công! Có thể tạo dự án từ hợp đồng này.'
        ]);
    }

    public function reject(Contract $contract)
    {
        $contract->update(['status' => 'rejected']);
        
        return back()->with('alert', [
            'type' => 'success',
            'message' => 'Đã từ chối hợp đồng.'
        ]);
    }

    public function edit(Contract $contract)
    {
        $employees = Employee::where('is_active', true)->get();
        return view('superadmin.contracts.edit', compact('contract', 'employees'));
    }

    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_code' => 'required|string|unique:contracts,contract_code,' . $contract->id,
            'client_name' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $fullCode = $employee->code . '/' . $request->contract_code;

        $data = [
            'employee_id' => $request->employee_id,
            'contract_code' => $request->contract_code,
            'full_code' => $fullCode,
            'start_date' => $contract->start_date,
            'end_date' => $request->deadline,
            'notes' => $request->notes,
            'is_active' => true,
        ];

        $contract->update($data);

        return redirect()->route('superadmin.contracts.index')->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật hợp đồng thành công!'
        ]);
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('superadmin.contracts.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa hợp đồng thành công!'
        ]);
    }
}

