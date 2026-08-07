<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');

        // Lấy token cấu hình trong .env (site vệ tinh)
        $expectedToken = config('app.sync_api_token', env('SYNC_API_TOKEN'));

        if (! $token || ! $expectedToken || $token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or invalid API token',
            ], 401);
        }

        return $next($request);
    }
}
