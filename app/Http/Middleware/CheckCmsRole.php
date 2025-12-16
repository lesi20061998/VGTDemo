<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
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

