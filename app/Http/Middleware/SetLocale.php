<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', setting('languages', [])[0]['code'] ?? 'vi');
        app()->setLocale($locale);
        return $next($request);
    }
}

