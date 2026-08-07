<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $tasks = Task::where('assigned_to', $employee->id)->with('project')->latest()->get();
        return view('employee.tasks.index', compact('tasks'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if ($task->assigned_to != $employee->id) {
            abort(403, 'Không có quyền cập nhật task này.');
        }

        $request->validate(['status' => 'required|in:todo,in_progress,review,done']);
        $task->update(['status' => $request->status]);

        return back()->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật trạng thái thành công!'
        ]);
    }
}

