<?php

// Debug Create Folder Functionality
echo "=== Debug Create Folder ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Testing Storage Disk Access...\n";
    
    $storage = \Illuminate\Support\Facades\Storage::disk('public');
    
    // Test basic storage operations
    try {
        $testPath = 'media/debug-test-' . time();
        
        echo "   Testing path: {$testPath}\n";
        
        // Check if we can create directory
        $result = $storage->makeDirectory($testPath);
        if ($result) {
            echo "   ✓ Successfully created test directory\n";
            
            // Check if directory exists
            if ($storage->exists($testPath)) {
                echo "   ✓ Directory exists after creation\n";
                
                // Clean up
                $storage->deleteDirectory($testPath);
                echo "   ✓ Successfully deleted test directory\n";
            } else {
                echo "   ✗ Directory does not exist after creation\n";
            }
        } else {
            echo "   ✗ Failed to create test directory\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ Storage error: " . $e->getMessage() . "\n";
    }
    
    echo "\n2. Testing MediaController createFolder Method...\n";
    
    // Create mock request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'name' => 'test-folder-' . time(),
        'path' => ''
    ]);
    
    // Test MediaController
    $controller = new App\Http\Controllers\Admin\MediaController();
    
    try {
        $response = $controller->createFolder($request);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            echo "   Response: " . json_encode($data) . "\n";
            
            if ($data['success'] ?? false) {
                echo "   ✓ MediaController createFolder succeeded\n";
            } else {
                echo "   ✗ MediaController createFolder failed: " . ($data['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "   ✗ MediaController returned non-JSON response\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ MediaController error: " . $e->getMessage() . "\n";
        echo "   Stack trace: " . $e->getTraceAsString() . "\n";
    }
    
    echo "\n3. Testing Path Generation...\n";
    
    // Test getMediaPath method
    $reflection = new ReflectionClass($controller);
    $getMediaPathMethod = $reflection->getMethod('getMediaPath');
    $getMediaPathMethod->setAccessible(true);
    
    // Test different scenarios
    $scenarios = [
        'No request' => null,
        'Empty request' => new \Illuminate\Http\Request(),
        'Request with project code' => (function() {
            $req = new \Illuminate\Http\Request();
            $req->setRouteResolver(function() {
                $route = new \Illuminate\Routing\Route(['GET'], '/test/admin/media/list', []);
                $route->bind(new \Illuminate\Http\Request());
                $route->setParameter('projectCode', 'testproject');
                return $route;
            });
            return $req;
        })()
    ];
    
    foreach ($scenarios as $name => $req) {
        try {
            $mediaPath = $getMediaPathMethod->invoke($controller, $req);
            echo "   {$name}: {$mediaPath}\n";
        } catch (Exception $e) {
            echo "   {$name}: Error - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n4. Testing Directory Permissions...\n";
    
    $storagePath = storage_path('app/public');
    $mediaPath = storage_path('app/public/media');
    
    // Check permissions
    $storagePerms = substr(sprintf('%o', fileperms($storagePath)), -4);
    echo "   Storage directory permissions: {$storagePerms}\n";
    
    if (is_dir($mediaPath)) {
        $mediaPerms = substr(sprintf('%o', fileperms($mediaPath)), -4);
        echo "   Media directory permissions: {$mediaPerms}\n";
    } else {
        echo "   Media directory does not exist\n";
    }
    
    // Test write permissions
    $testFile = $storagePath . '/test-write-' . time() . '.txt';
    if (file_put_contents($testFile, 'test')) {
        echo "   ✓ Can write to storage directory\n";
        unlink($testFile);
    } else {
        echo "   ✗ Cannot write to storage directory\n";
    }
    
    echo "\n5. Testing Full Path Construction...\n";
    
    // Test different path combinations
    $testCases = [
        ['basePath' => 'media', 'path' => '', 'name' => 'newfolder'],
        ['basePath' => 'media', 'path' => 'subfolder', 'name' => 'newfolder'],
        ['basePath' => 'media/project-test', 'path' => '', 'name' => 'newfolder'],
        ['basePath' => 'media/project-test', 'path' => 'uploads', 'name' => 'newfolder']
    ];
    
    foreach ($testCases as $i => $case) {
        $basePath = $case['basePath'];
        $path = $case['path'];
        $name = $case['name'];
        
        $fullPath = $path ? $basePath.'/'.ltrim($path, '/').'/'.$name : $basePath.'/'.$name;
        echo "   Case " . ($i + 1) . ": {$fullPath}\n";
        
        // Test if this path would work
        try {
            if ($storage->makeDirectory($fullPath)) {
                echo "     ✓ Path creation successful\n";
                $storage->deleteDirectory($fullPath);
            } else {
                echo "     ✗ Path creation failed\n";
            }
        } catch (Exception $e) {
            echo "     ✗ Path error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n6. Checking Laravel Storage Configuration...\n";
    
    $config = config('filesystems.disks.public');
    echo "   Driver: " . $config['driver'] . "\n";
    echo "   Root: " . $config['root'] . "\n";
    echo "   URL: " . $config['url'] . "\n";
    echo "   Visibility: " . ($config['visibility'] ?? 'public') . "\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";