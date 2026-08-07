<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->paginate(20);

        return view('superadmin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('superadmin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'group' => 'required|string',
        ]);

        Permission::create($validated);

        return redirect()->route('superadmin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('superadmin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,'.$permission->id,
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'group' => 'required|string',
        ]);

        $permission->update($validated);

        return redirect()->route('superadmin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('superadmin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
