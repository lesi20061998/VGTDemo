<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Middleware is now handled in routes or via attributes in Laravel 12

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'activityLogs' => function ($q) {
            $q->latest()->limit(5);
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $users = $query->paginate(15);
        $roles = Role::all();

        return view('cms.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('cms.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'status' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $request->boolean('status', true);

        $user = User::create($validated);

        // Assign roles
        if (! empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_created',
                "Created user: {$user->name} ({$user->email})",
                null,
                'User',
                $user->id
            );
        }

        // Send welcome email (optional)
        if ($request->boolean('send_welcome_email')) {
            // Mail::to($user)->send(new WelcomeEmail($user));
        }

        return redirect()->route('cms.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'permissions', 'activityLogs' => function ($q) {
            $q->latest()->limit(20);
        }]);

        return view('cms.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('cms.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'status' => 'boolean',
            'preferences' => 'nullable|array',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Only update password if provided
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['status'] = $request->boolean('status', true);

        $user->update($validated);

        // Update roles
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_updated',
                "Updated user: {$user->name} ({$user->email})",
                null,
                'User',
                $user->id
            );
        }

        return redirect()->route('cms.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting super admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('cms.users.index')
                ->with('error', 'Cannot delete super administrator.');
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('cms.users.index')
                ->with('error', 'Cannot delete your own account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_deleted',
                "Deleted user: {$userName} ({$userEmail})",
                null,
                'User',
                $user->id
            );
        }

        return redirect()->route('cms.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        if (! $user->roles->contains($role)) {
            $user->roles()->attach($role);

            if (! app()->environment('testing')) {
                ActivityLog::log(
                    'role_assigned',
                    "Assigned role '{$role->display_name}' to user: {$user->name}",
                    null,
                    'User',
                    $user->id
                );
            }

            return response()->json(['success' => true, 'message' => 'Role assigned successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'User already has this role.']);
    }

    /**
     * Revoke role from user.
     */
    public function revokeRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        if ($user->roles->contains($role)) {
            $user->roles()->detach($role);

            if (! app()->environment('testing')) {
                ActivityLog::log(
                    'role_revoked',
                    "Revoked role '{$role->display_name}' from user: {$user->name}",
                    null,
                    'User',
                    $user->id
                );
            }

            return response()->json(['success' => true, 'message' => 'Role revoked successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'User does not have this role.']);
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent disabling super admin
        if ($user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot disable super administrator.']);
        }

        // Prevent self-disabling
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Cannot disable your own account.']);
        }

        $user->update(['status' => ! $user->status]);

        $action = $user->status ? 'activated' : 'deactivated';

        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_status_changed',
                "User {$action}: {$user->name} ({$user->email})",
                null,
                'User',
                $user->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => "User {$action} successfully.",
            'status' => $user->status,
        ]);
    }
}
