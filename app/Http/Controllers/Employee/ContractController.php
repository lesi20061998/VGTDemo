<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Contract;

class ContractController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $contracts = Contract::where('employee_id', $employee->id)->with('website')->latest()->get();
        return view('employee.contracts.index', compact('contracts'));
    }
}

