<?php

// Debug Media API Issues
echo "=== Debug Media API Issues ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking Current Environment...\n";
    echo "   Environment: " . app()->environment() . "\n";
    echo "   App URL: " . config('app.url') . "\n";
    echo "   Debug Mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
    
    echo "\n2. Testing Media Routes Registration...\n";
    
    $router = app('router');
    $routes = $router->getRoutes();
    
    $mediaRoutes = [];
    foreach ($routes as $route) {
        $name = $route->getName();
        if ($name && strpos($name, 'media') !== false) {
            $mediaRoutes[] = [
                'name' => $name,
                'uri' => $route->uri(),
                'methods' => implode('|', $route->methods()),
                'middleware' => implode(', ', $route->middleware())
            ];
        }
    }
    
    echo "   Found " . count($mediaRoutes) . " media routes:\n";
    foreach ($mediaRoutes as $route) {
        echo "   - {$route['name']}: {$route['methods']} {$route['uri']}\n";
        echo "     Middleware: {$route['middleware']}\n";
    }
    
    echo "\n3. Testing Authentication Status...\n";
    
    // Check if we have any authenticated user
    if (auth()->check()) {
        $user = auth()->user();
        echo "   ✓ User authenticated:\n";
        echo "     ID: " . $user->id . "\n";
        echo "     Username: " . ($user->username ?? 'N/A') . "\n";
        echo "     Role: " . ($user->role ?? 'N/A') . "\n";
        echo "     Level: " . ($user->level ?? 'N/A') . "\n";
    } else {
        echo "   ✗ No authenticated user\n";
    }
    
    echo "\n4. Testing Session Configuration...\n";
    
    echo "   Session driver: " . config('session.driver') . "\n";
    echo "   Session lifetime: " . config('session.lifetime') . " minutes\n";
    echo "   Session domain: " . config('session.domain') . "\n";
    echo "   Session path: " . config('session.path') . "\n";
    echo "   Session secure: " . (config('session.secure') ? 'YES' : 'NO') . "\n";
    echo "   Session same_site: " . config('session.same_site') . "\n";
    
    echo "\n5. Testing CSRF Configuration...\n";
    
    echo "   CSRF token: " . csrf_token() . "\n";
    
    echo "\n6. Simulating Media API Calls...\n";
    
    // Test CMS media list endpoint
    echo "   Testing CMS media list...\n";
    try {
        $request = \Illuminate\Http\Request::create('/admin/media/list', 'GET');
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        // Simulate authenticated request
        if (auth()->check()) {
            $request->setUserResolver(function() {
                return auth()->user();
            });
        }
        
        $response = app()->handle($request);
        
        echo "     Status: " . $response->getStatusCode() . "\n";
        echo "     Content-Type: " . $response->headers->get('Content-Type') . "\n";
        
        $content = $response->getContent();
        if (strpos($content, '<!DOCTYPE') === 0) {
            echo "     ✗ Response is HTML (likely redirect to login)\n";
            echo "     First 200 chars: " . substr($content, 0, 200) . "...\n";
        } elseif (json_decode($content)) {
            echo "     ✓ Response is valid JSON\n";
            $data = json_decode($content, true);
            echo "     Folders: " . count($data['folders'] ?? []) . "\n";
            echo "     Files: " . count($data['files'] ?? []) . "\n";
        } else {
            echo "     ✗ Response is neither HTML nor JSON\n";
            echo "     Content: " . substr($content, 0, 200) . "...\n";
        }
        
    } catch (Exception $e) {
        echo "     ✗ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n7. Testing Project Media Endpoint...\n";
    
    // Test project media list endpoint
    try {
        $request = \Illuminate\Http\Request::create('/SiVGT/admin/media/list', 'GET');
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        $response = app()->handle($request);
        
        echo "     Status: " . $response->getStatusCode() . "\n";
        echo "     Content-Type: " . $response->headers->get('Content-Type') . "\n";
        
        $content = $response->getContent();
        if (strpos($content, '<!DOCTYPE') === 0) {
            echo "     ✗ Response is HTML (likely redirect to login)\n";
            echo "     First 200 chars: " . substr($content, 0, 200) . "...\n";
        } elseif (json_decode($content)) {
            echo "     ✓ Response is valid JSON\n";
        } else {
            echo "     ✗ Response is neither HTML nor JSON\n";
            echo "     Content: " . substr($content, 0, 200) . "...\n";
        }
        
    } catch (Exception $e) {
        echo "     ✗ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n8. Checking Middleware Configuration...\n";
    
    $middlewareAliases = app('router')->getMiddleware();
    $importantMiddleware = ['auth', 'cms', 'project', 'web'];
    
    foreach ($importantMiddleware as $alias) {
        if (isset($middlewareAliases[$alias])) {
            echo "   ✓ {$alias}: " . $middlewareAliases[$alias] . "\n";
        } else {
            echo "   ✗ {$alias}: Not registered\n";
        }
    }
    
    echo "\n9. Checking Current Request Context...\n";
    
    $currentRequest = request();
    echo "   Current URL: " . $currentRequest->url() . "\n";
    echo "   Current Route: " . ($currentRequest->route() ? $currentRequest->route()->getName() : 'N/A') . "\n";
    echo "   User Agent: " . $currentRequest->userAgent() . "\n";
    echo "   Is AJAX: " . ($currentRequest->ajax() ? 'YES' : 'NO') . "\n";
    echo "   Accepts JSON: " . ($currentRequest->acceptsJson() ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";
echo "\nRECOMMENDATIONS:\n";
echo "1. If authentication is the issue, make sure user is logged in properly\n";
echo "2. Check Laravel logs in storage/logs/ for detailed error messages\n";
echo "3. Verify CSRF token is being sent with AJAX requests\n";
echo "4. Make sure Accept: application/json header is set in frontend requests\n";
echo "5. Check if middleware is redirecting unauthenticated requests\n";