<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure we're using main database for superadmin
        $this->ensureMainDatabase();

        // Use web guard for superadmin (main database)
        if (! Auth::guard('web')->check()) {
            return redirect('/login');
        }

        $user = Auth::guard('web')->user();

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

    /**
     * Ensure we're connected to main database
     */
    private function ensureMainDatabase(): void
    {
        if (Config::get('database.default') !== 'mysql') {
            DB::setDefaultConnection('mysql');
            Config::set('database.default', 'mysql');
        }
    }
}
