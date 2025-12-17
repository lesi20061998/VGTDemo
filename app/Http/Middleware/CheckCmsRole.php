<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCmsRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $projectCode = $request->route('projectCode');

        // For project routes, check session-based auth
        if ($projectCode) {
            $userId = session('project_user_id');

            if (! $userId) {
                return redirect()->route('project.login', ['projectCode' => $projectCode]);
            }

            // Load user from project database and share with views
            $user = \App\Models\ProjectUser::find($userId);

            if (! $user) {
                session()->forget(['project_user_id', 'project_user_username', 'current_project']);

                return redirect()->route('project.login', ['projectCode' => $projectCode]);
            }

            // Share user with views
            view()->share('authUser', $user);
            $request->attributes->set('auth_user', $user);

            // Allow superadmin (level=0) and administrator (level=1)
            if (isset($user->level) && in_array($user->level, [0, 1])) {
                return $next($request);
            }

            // Allow all users with cms or admin role
            if (isset($user->role) && in_array($user->role, ['cms', 'admin'])) {
                return $next($request);
            }

            abort(403, 'Bạn không có quyền truy cập CMS này.');
        }

        // For non-project routes, use web guard
        if (! Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();

        // Allow superadmin (level=0) and administrator (level=1)
        if (isset($user->level) && in_array($user->level, [0, 1])) {
            return $next($request);
        }

        // Allow all users with cms or admin role to access all CMS
        if (isset($user->role) && in_array($user->role, ['cms', 'admin'])) {
            return $next($request);
        }

        // Deny access for other users
        abort(403, 'Bạn không có quyền truy cập CMS này.');
    }
}
