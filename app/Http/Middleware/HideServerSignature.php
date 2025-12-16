<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HideServerSignature
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Xóa các headers tiết lộ thông tin
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->set('X-Powered-By', 'VGT System');
        
        return $response;
    }
}

