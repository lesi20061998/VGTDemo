<?php

// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = auth()->user();

            if (isset($user->role) && in_array($user->role, ['super_admin', 'superadmin'])) {
                return redirect('/superadmin');
            }

            // Level 0, 1, 2 (superadmin, account, dev) go to superadmin board
            if (isset($user->level) && $user->level <= 2) {
                return redirect('/superadmin');
            }

            if (isset($user->role) && in_array($user->role, ['cms', 'admin'])) {
                return redirect('/cms/admin');
            }

            if (isset($user->role) && $user->role === 'employee') {
                return redirect('/employee');
            }

            return redirect('/superadmin');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = auth()->user();

            // SuperAdmin role -> SuperAdmin panel
            if (isset($user->role) && in_array($user->role, ['super_admin', 'superadmin'])) {
                return redirect('/superadmin');
            }

            // Level 0, 1, 2 -> SuperAdmin panel
            if (isset($user->level) && $user->level <= 2) {
                return redirect('/superadmin');
            }

            // CMS User -> CMS panel
            if (isset($user->role) && in_array($user->role, ['cms', 'admin'])) {
                return redirect('/cms/admin');
            }

            // Employee -> Employee panel
            if (isset($user->role) && $user->role === 'employee') {
                return redirect('/employee');
            }

            return redirect('/superadmin');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
