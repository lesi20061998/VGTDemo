<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-tasks') && ! $user->hasPermission('update-tasks-progress') && ! $user->hasPermission('review-tasks') && $user->role !== 'dev' && ! $user->hasRole('dev')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return view('superadmin.tasks.index');
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-tasks')) {
            abort(403, 'Bạn không có quyền tạo Task.');
        }

        if ($user->hasPermission('manage-tasks') && ! $user->isSuperAdmin()) {
            $projects = Project::where('admin_id', $user->id)->get();
        } else {
            $projects = Project::all();
        }

        $devs = User::whereHas('roles', function ($q) {
            $q->where('name', 'dev');
        })->orWhere('role', 'dev')->get();

        $selectedProject = $request->project_id;

        return view('superadmin.tasks.create', compact('projects', 'devs', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dev_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $validated['status'] = 'todo';

        Task::create($validated);

        return redirect()->route('superadmin.tasks.index')->with('success', 'Task assigned successfully.');
    }

    public function show(Task $task)
    {
        return view('superadmin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $user = auth()->user();

        if ($user->hasPermission('update-tasks-progress') && ! $user->isSuperAdmin() && ! $user->hasPermission('manage-tasks') && ! $user->hasPermission('review-tasks')) {
            // Devs can only edit status and result_notes
            return view('superadmin.tasks.edit_dev', compact('task'));
        }

        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-tasks') && ! $user->hasPermission('review-tasks')) {
            abort(403, 'Bạn không có quyền sửa Task.');
        }

        if (($user->hasPermission('manage-tasks') || $user->hasPermission('review-tasks')) && ! $user->isSuperAdmin()) {
            $projects = Project::where('admin_id', $user->id)->get();
        } else {
            $projects = Project::all();
        }

        $devs = User::whereHas('roles', function ($q) {
            $q->where('name', 'dev');
        })->orWhere('role', 'dev')->get();

        return view('superadmin.tasks.edit', compact('task', 'projects', 'devs'));
    }

    public function update(Request $request, Task $task)
    {
        $user = auth()->user();

        // If user is Dev (has update-tasks-progress but not manage-tasks or review-tasks)
        if ($user->hasPermission('update-tasks-progress') && ! $user->isSuperAdmin() && ! $user->hasPermission('manage-tasks') && ! $user->hasPermission('review-tasks')) {
            $validated = $request->validate([
                'status' => 'required|in:todo,in_progress,review',
                'result_notes' => 'nullable|string',
            ]);
            $task->update($validated);

            return redirect()->route('superadmin.tasks.index')->with('success', 'Task progress updated.');
        }

        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-tasks') && ! $user->hasPermission('review-tasks')) {
            abort(403, 'Bạn không có quyền cập nhật Task.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dev_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,review,rework,done',
            'result_notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('superadmin.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-tasks')) {
            abort(403, 'Bạn không có quyền xóa Task.');
        }
        $task->delete();

        return redirect()->route('superadmin.tasks.index')->with('success', 'Task deleted successfully.');
    }
}
