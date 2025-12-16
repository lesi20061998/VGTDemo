<?php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;

class LogVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Chỉ log 1 lần mỗi session, chỉ GET, không AJAX
        if ($request->method() === 'GET' && 
            !$request->ajax() && 
            !session()->has('visitor_logged_' . date('Y-m-d-H'))) {
            
            $url = $request->fullUrl();
            
            // Bỏ qua admin, assets, api
            if (!str_contains($url, '/admin') && 
                !str_contains($url, '/assets/') && 
                !str_contains($url, '/api/')) {
                
                VisitorLog::create([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $url,
                    'method' => $request->method(),
                    'user_id' => auth()->id(),
                    'visited_at' => now()
                ]);
                
                session(['visitor_logged_' . date('Y-m-d-H') => true]);
            }
        }

        return $next($request);
    }
}