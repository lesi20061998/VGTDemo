<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectLoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $project = $request->attributes->get('project');
        
        if (!$project) {
            abort(404, 'Dự án không tồn tại.');
        }
        
        if (Auth::check()) {
            return redirect('/' . $project->code . '/admin');
        }
        
        return view('auth.project-login', compact('project'));
    }

    public function login(Request $request)
    {
        $project = $request->attributes->get('project');
        
        if (!$project) {
            abort(404, 'Dự án không tồn tại.');
        }
        
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Try login with username first, then email
        $loginField = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $loginCredentials = [
            $loginField => $credentials['username'],
            'password' => $credentials['password']
        ];

        if (Auth::attempt($loginCredentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/' . $project->code . '/admin');
        }
        
        // If first attempt fails and we tried username, try with email
        if ($loginField === 'username') {
            $emailCredentials = [
                'email' => $credentials['username'],
                'password' => $credentials['password']
            ];
            if (Auth::attempt($emailCredentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('/' . $project->code . '/admin');
            }
        }

        return back()->withErrors([
            'username' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $project = $request->attributes->get('project');
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('project.login', $project ? $project->code : 'default');
    }
}

