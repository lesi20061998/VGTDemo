<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        
        // Cho phép: level 0-1 HOẶC role=superadmin với employee có superadmin_role
        if ($user->level <= 1) {
            return $next($request);
        }
        
        if ($user->role === 'superadmin' && $user->employee) {
            // Cho phép: superadmin_role hoặc quản lý bộ phận
            if (in_array($user->employee->superadmin_role, ['superadmin', 'director', 'account', 'dev']) || $user->employee->is_department_manager) {
                return $next($request);
            }
        }
        
        abort(403, 'Bạn không có quyền truy cập SuperAdmin.');
    }
}

