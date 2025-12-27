<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ProjectSession
{
    /**
     * Handle an incoming request.
     * Set separate session cookie for project routes to isolate from superadmin
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract projectCode from URL path (first segment)
        // This runs BEFORE route binding, so we parse the path directly
        $path = trim($request->getPathInfo(), '/');
        $segments = explode('/', $path);
        $projectCode = $segments[0] ?? null;

        // Only apply if this looks like a project route (not superadmin, admin, login, etc.)
        $reservedPrefixes = ['superadmin', 'admin', 'login', 'logout', 'api', 'employee', 'lang', 'sitemap', 'livewire'];

        // For Livewire requests, get projectCode from referer
        if ($projectCode === 'livewire') {
            $referer = $request->headers->get('referer');
            if ($referer) {
                $refererPath = parse_url($referer, PHP_URL_PATH);
                $refererSegments = explode('/', trim($refererPath, '/'));
                $projectCode = $refererSegments[0] ?? null;
                
                // Check if referer projectCode is valid (not reserved)
                if ($projectCode && !in_array($projectCode, $reservedPrefixes)) {
                    $cookieName = 'project_'.strtolower($projectCode).'_session';
                    Config::set('session.cookie', $cookieName);
                }
            }
            return $next($request);
        }

        if ($projectCode && ! in_array($projectCode, $reservedPrefixes)) {
            // Set unique session cookie name for this project
            $cookieName = 'project_'.strtolower($projectCode).'_session';
            Config::set('session.cookie', $cookieName);

            // Don't set session path - it can cause redirect loops
            // Config::set('session.path', '/' . $projectCode);
        }

        return $next($request);
    }
}
