<?php

// Debug Media Upload 404 Error
echo "=== Debug Media Upload 404 ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking Media Routes...\n";
    
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
                'action' => $route->getActionName()
            ];
        }
    }
    
    echo "   Found " . count($mediaRoutes) . " media routes:\n";
    foreach ($mediaRoutes as $route) {
        echo "   - {$route['name']}: {$route['methods']} /{$route['uri']}\n";
    }
    
    echo "\n2. Testing Route URLs...\n";
    
    try {
        $cmsMediaList = route('cms.media.list');
        echo "   CMS Media List: {$cmsMediaList}\n";
        
        $cmsMediaUpload = route('cms.media.upload');
        echo "   CMS Media Upload: {$cmsMediaUpload}\n";
        
        $projectMediaList = route('project.admin.media.list', ['projectCode' => 'SiVGT']);
        echo "   Project Media List: {$projectMediaList}\n";
        
        $projectMediaUpload = route('project.admin.media.upload', ['projectCode' => 'SiVGT']);
        echo "   Project Media Upload: {$projectMediaUpload}\n";
        
    } catch (Exception $e) {
        echo "   ✗ Route error: " . $e->getMessage() . "\n";
    }
    
    echo "\n3. Checking Authentication...\n";
    
    if (auth()->check()) {
        echo "   ✓ User authenticated: " . auth()->user()->username . "\n";
        echo "   Role: " . (auth()->user()->role ?? 'N/A') . "\n";
        echo "   Level: " . (auth()->user()->level ?? 'N/A') . "\n";
    } else {
        echo "   ✗ No user authenticated\n";
        echo "   This could cause 404 errors due to auth middleware\n";
    }
    
    echo "\n4. Testing Storage Paths...\n";
    
    $storagePath = storage_path('app/public');
    $mediaPath = storage_path('app/public/media');
    
    echo "   Storage path: {$storagePath}\n";
    echo "   Storage exists: " . (is_dir($storagePath) ? 'YES' : 'NO') . "\n";
    echo "   Storage writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";
    
    echo "   Media path: {$mediaPath}\n";
    echo "   Media exists: " . (is_dir($mediaPath) ? 'YES' : 'NO') . "\n";
    echo "   Media writable: " . (is_writable($mediaPath) ? 'YES' : 'NO') . "\n";
    
    // Check public storage symlink
    $publicStorage = public_path('storage');
    echo "   Public storage: {$publicStorage}\n";
    echo "   Public storage exists: " . (file_exists($publicStorage) ? 'YES' : 'NO') . "\n";
    
    echo "\n5. Testing MediaController Directly...\n";
    
    try {
        $controller = new App\Http\Controllers\Admin\MediaController();
        
        // Test list method
        $request = new \Illuminate\Http\Request();
        $request->merge(['path' => '']);
        
        $response = $controller->list($request);
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            echo "   ✓ MediaController list works\n";
            echo "     Folders: " . count($data['folders'] ?? []) . "\n";
            echo "     Files: " . count($data['files'] ?? []) . "\n";
        } else {
            echo "   ✗ MediaController list failed\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ MediaController error: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Simulating Upload Request...\n";
    
    // Create a mock upload request
    try {
        $uploadRequest = \Illuminate\Http\Request::create('/admin/media/upload', 'POST');
        $uploadRequest->headers->set('Accept', 'application/json');
        $uploadRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
        $uploadRequest->headers->set('X-CSRF-TOKEN', csrf_token());
        
        // Simulate authentication
        if (auth()->check()) {
            $uploadRequest->setUserResolver(function() {
                return auth()->user();
            });
        }
        
        echo "   Upload URL: /admin/media/upload\n";
        echo "   CSRF Token: " . csrf_token() . "\n";
        echo "   Accept Header: application/json\n";
        
        // Test route resolution
        $route = $router->getRoutes()->match($uploadRequest);
        if ($route) {
            echo "   ✓ Route matched: " . $route->getName() . "\n";
            echo "   Action: " . $route->getActionName() . "\n";
            echo "   Middleware: " . implode(', ', $route->middleware()) . "\n";
        } else {
            echo "   ✗ No route matched - This causes 404!\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ Route matching error: " . $e->getMessage() . "\n";
    }
    
    echo "\n7. Checking .htaccess Impact...\n";
    
    $htaccessPath = base_path('.htaccess');
    if (file_exists($htaccessPath)) {
        $htaccessContent = file_get_contents($htaccessPath);
        echo "   .htaccess exists\n";
        
        if (strpos($htaccessContent, 'public/') !== false) {
            echo "   ✓ Contains public/ redirects\n";
        } else {
            echo "   ✗ No public/ redirects found\n";
        }
        
        if (strpos($htaccessContent, 'RewriteEngine On') !== false) {
            echo "   ✓ Rewrite engine enabled\n";
        } else {
            echo "   ✗ Rewrite engine not found\n";
        }
    } else {
        echo "   ✗ .htaccess not found\n";
    }
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";
echo "\nPOSSIBLE CAUSES OF 404:\n";
echo "1. Authentication required - user not logged in\n";
echo "2. Route not matching due to .htaccess changes\n";
echo "3. CSRF token missing or invalid\n";
echo "4. Wrong URL being called by frontend\n";
echo "5. Middleware blocking the request\n";
echo "\nNEXT STEPS:\n";
echo "1. Make sure you're logged in\n";
echo "2. Check browser network tab for exact URL being called\n";
echo "3. Verify CSRF token in request headers\n";
echo "4. Test with /public/ in URL to see if it works\n";