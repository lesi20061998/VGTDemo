<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use App\Models\Project;
use Illuminate\Http\Request;

class BriefController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-briefs')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        if ($user->hasPermission('manage-briefs') && ! $user->isSuperAdmin()) {
            $briefs = Brief::where('account_id', $user->id)->latest()->paginate(15);
        } else {
            $briefs = Brief::with('account')->latest()->paginate(15);
        }

        return view('superadmin.briefs.index', compact('briefs'));
    }

    public function create()
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-briefs')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return view('superadmin.briefs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'requirements' => 'required|string',
            'budget' => 'nullable|numeric',
            'deadline' => 'nullable|date',
        ]);

        $validated['account_id'] = auth()->id();
        $validated['status'] = 'draft';

        Brief::create($validated);

        return redirect()->route('superadmin.briefs.index')->with('success', 'Brief created successfully.');
    }

    public function show(Brief $brief)
    {
        return view('superadmin.briefs.show', compact('brief'));
    }

    public function edit(Brief $brief)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasPermission('manage-briefs')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return view('superadmin.briefs.edit', compact('brief'));
    }

    public function update(Request $request, Brief $brief)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'requirements' => 'required|string',
            'budget' => 'nullable|numeric',
            'deadline' => 'nullable|date',
            'status' => 'required|in:draft,submitted,approved,rejected',
        ]);

        $user = auth()->user();
        if ($validated['status'] == 'approved' && ! $user->isSuperAdmin() && ! $user->hasPermission('approve-briefs')) {
            abort(403, 'Bạn không có quyền duyệt Brief thành Project.');
        }

        $brief->update($validated);

        if ($validated['status'] == 'approved') {
            // Check if project exists
            $exists = Project::where('name', clone $brief->title)->first();
            if (! $exists) {
                // Generate a dummy code
                $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $brief->client_name), 0, 5)).rand(100, 999);
                Project::create([
                    'name' => $brief->title,
                    'code' => $code,
                    'client_name' => $brief->client_name,
                    'technical_requirements' => $brief->requirements,
                    'contract_value' => $brief->budget,
                    'deadline' => $brief->deadline,
                    'admin_id' => $brief->account_id,
                    'created_by' => auth()->id(),
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('superadmin.briefs.index')->with('success', 'Brief updated successfully.');
    }

    public function destroy(Brief $brief)
    {
        $brief->delete();

        return redirect()->route('superadmin.briefs.index')->with('success', 'Brief deleted successfully.');
    }
}
