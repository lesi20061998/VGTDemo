<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SuperAdminBypassProjectScope
{
    public function handle(Request $request, Closure $next)
    {
        // Nếu là SuperAdmin, bypass project scope
        if (auth()->check() && auth()->user()->user_level === 'superadmin') {
            // Tắt global scope cho project_id
            Config::set('app.bypass_project_scope', true);
        }

        return $next($request);
    }
}