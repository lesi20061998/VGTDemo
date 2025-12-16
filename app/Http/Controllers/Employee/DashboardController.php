<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Contract;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            abort(403, 'Không tìm thấy thông tin nhân viên.');
        }

        $myTasks = Task::where('assigned_to', $employee->id)
            ->with('project')
            ->latest()
            ->take(10)
            ->get();

        $myContracts = Contract::where('employee_id', $employee->id)
            ->with('website')
            ->latest()
            ->take(10)
            ->get();

        $taskStats = [
            'todo' => Task::where('assigned_to', $employee->id)->where('status', 'todo')->count(),
            'in_progress' => Task::where('assigned_to', $employee->id)->where('status', 'in_progress')->count(),
            'review' => Task::where('assigned_to', $employee->id)->where('status', 'review')->count(),
            'done' => Task::where('assigned_to', $employee->id)->where('status', 'done')->count(),
        ];

        return view('employee.dashboard', compact('employee', 'myTasks', 'myContracts', 'taskStats'));
    }
}

