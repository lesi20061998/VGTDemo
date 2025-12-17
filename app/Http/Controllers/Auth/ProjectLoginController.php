<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        // Find user directly from project database
        $user = \App\Models\ProjectUser::where('username', $credentials['username'])
            ->orWhere('email', $credentials['username'])
            ->first();

        if ($user && \Hash::check($credentials['password'], $user->password)) {
            // Store user ID in session for manual authentication
            $request->session()->put('project_user_id', $user->id);
            $request->session()->put('project_user_username', $user->username);
            $request->session()->regenerate();

            \Log::info("Project login success: {$user->username} for project {$project->code}");

            return redirect()->intended('/'.$project->code.'/admin');
        }

        \Log::warning("Project login failed for username: {$credentials['username']}");

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
