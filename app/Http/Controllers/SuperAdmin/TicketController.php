<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProjectTicket;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = ProjectTicket::with(['project', 'creator', 'assignedTo'])->latest()->paginate(20);
        return view('superadmin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $projects = Project::all();
        $employees = Employee::where('is_active', true)->get();
        return view('superadmin.tickets.create', compact('projects', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:feedback,support,bug,feature',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        ProjectTicket::create([
            'project_id' => $request->project_id,
            'created_by' => auth()->id(),
            'assigned_to' => $request->assigned_to,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()->route('superadmin.tickets.index')->with('alert', [
            'type' => 'success',
            'message' => 'Tạo ticket thành công!'
        ]);
    }

    public function show(ProjectTicket $ticket)
    {
        $ticket->load(['project', 'creator', 'assignedTo']);
        return view('superadmin.tickets.show', compact('ticket'));
    }

    public function edit(ProjectTicket $ticket)
    {
        $projects = Project::all();
        $employees = Employee::where('is_active', true)->get();
        return view('superadmin.tickets.edit', compact('ticket', 'projects', 'employees'));
    }

    public function update(Request $request, ProjectTicket $ticket)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $data = $request->only(['title', 'description', 'status', 'priority', 'assigned_to', 'resolution']);
        
        if ($request->status === 'resolved' && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }

        $ticket->update($data);

        return redirect()->route('superadmin.tickets.show', $ticket)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật ticket thành công!'
        ]);
    }
}

