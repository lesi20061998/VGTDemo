<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project', 'assignedTo'])->latest()->paginate(20);
        return view('superadmin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $employees = Employee::where('is_active', true)->get();
        return view('superadmin.tasks.create', compact('projects', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        Task::create($request->all());

        return redirect()->route('superadmin.tasks.index')->with('alert', [
            'type' => 'success',
            'message' => 'Tạo task thành công!'
        ]);
    }

    public function edit(Task $task)
    {
        $projects = Project::where('status', 'active')->get();
        $employees = Employee::where('is_active', true)->get();
        return view('superadmin.tasks.edit', compact('task', 'projects', 'employees'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->all());

        return redirect()->route('superadmin.tasks.index')->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật task thành công!'
        ]);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('superadmin.tasks.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa task thành công!'
        ]);
    }
}

