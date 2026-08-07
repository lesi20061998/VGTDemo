<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectLoginController extends Controller
{
    /**
     * Get the guard for project authentication
     */
    protected function guard()
    {
        return Auth::guard('project');
    }

    public function showLoginForm(Request $request)
    {
        $project = $request->attributes->get('project');

        if (! $project) {
            abort(404, 'Dự án không tồn tại.');
        }

        // Check if already logged in via session
        if (session('project_user_id') && session('current_project') === $project->code) {
            return redirect('/'.$project->code.'/admin');
        }

        return view('auth.project-login', compact('project'));
    }

    public function login(Request $request)
    {
        $project = $request->attributes->get('project');

        if (! $project) {
            abort(404, 'Dự án không tồn tại.');
        }

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Store project code in session for later use
        $request->session()->put('current_project', $project->code);

        // DEMO MODE: Find user in shared database, scoped to this project
        // Force 'mysql' connection + bypass tenant scope because:
        // 1. SetProjectDatabase middleware switches default connection to 'project'
        // 2. BelongsToTenant adds WHERE tenant_id = session(current_tenant_id) but CMS users have tenant_id = NULL
        $user = User::on('mysql')
            ->withoutGlobalScope('tenant')
            ->where(function ($q) use ($credentials) {
                $q->where('username', $credentials['username'])
                    ->orWhere('email', $credentials['username']);
            })
            ->where(function ($q) {
                $q->whereIn('role', ['cms', 'admin', 'dev', 'super_admin', 'superadmin']);
            })
            ->get()
            ->first(function ($u) use ($project) {
                // Super admin and Dev can access any project
                if (in_array($u->role, ['super_admin', 'superadmin', 'dev'])) {
                    return true;
                }

                // Check if this user is scoped to this project
                $projectIds = is_array($u->project_ids) ? $u->project_ids : json_decode($u->project_ids ?? '[]', true);

                return in_array($project->id, $projectIds ?? []);
            });

        if ($user && \Hash::check($credentials['password'], $user->password)) {
            // Store user ID in session for manual authentication
            $request->session()->put('project_user_id', $user->id);
            $request->session()->put('project_user_username', $user->username);
            $request->session()->regenerate();

            \Log::info("Project login success: {$user->username} for project {$project->code}");

            return redirect()->intended('/'.$project->code.'/admin');
        }

        \Log::warning("Project login failed for username: {$credentials['username']} on project: {$project->code}");

        return back()->withErrors([
            'username' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $project = $request->attributes->get('project');

        // Clear session-based auth
        $request->session()->forget(['project_user_id', 'project_user_username', 'current_project']);
        $request->session()->regenerateToken();

        return redirect()->route('project.login', ['projectCode' => $project ? $project->code : 'default']);
    }
}
