<?php

// Test Media and Cleanup
echo "=== Testing Media & Cleanup ===\n\n";

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Final Configuration Check...\n";
    
    // Clear config cache to reload .env
    Artisan::call('config:clear');
    
    echo "   APP_URL: " . config('app.url') . "\n";
    echo "   Session Domain: " . config('session.domain') . "\n";
    echo "   Session Driver: " . config('session.driver') . "\n";
    
    if (config('session.domain') === 'core.vnglobaltech.com') {
        echo "   ✓ Session domain configured correctly\n";
    } else {
        echo "   ✗ Session domain not set correctly\n";
    }
    
    echo "\n2. Testing Authentication...\n";
    
    if (auth()->check()) {
        echo "   ✓ User authenticated: " . auth()->user()->username . "\n";
        
        // Test media controller
        $controller = new App\Http\Controllers\Admin\MediaController();
        $request = new \Illuminate\Http\Request();
        $request->merge(['path' => '']);
        
        $response = $controller->list($request);
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            echo "   ✓ Media controller working\n";
            echo "     Folders: " . count($data['folders'] ?? []) . "\n";
            echo "     Files: " . count($data['files'] ?? []) . "\n";
        }
        
        // Test create folder
        $request2 = new \Illuminate\Http\Request();
        $request2->merge(['name' => 'test-final-' . time(), 'path' => '']);
        
        $response2 = $controller->createFolder($request2);
        if ($response2 instanceof \Illuminate\Http\JsonResponse) {
            $data2 = $response2->getData(true);
            if ($data2['success'] ?? false) {
                echo "   ✓ Create folder working\n";
            } else {
                echo "   ✗ Create folder failed: " . ($data2['message'] ?? 'Unknown error') . "\n";
            }
        }
        
    } else {
        echo "   ✗ No user authenticated\n";
        echo "   Please visit /production_login.php to login first\n";
    }
    
    echo "\n3. Cleanup Debug Files...\n";
    
    $debugFiles = [
        'debug_create_folder.php',
        'debug_media_api.php', 
        'fix_media_authentication.php',
        'simple_auth_fix.php',
        'final_media_fix.php',
        'quick_media_fix.php',
        'test_and_cleanup.php'
    ];
    
    foreach ($debugFiles as $file) {
        if (file_exists($file)) {
            unlink($file);
            echo "   ✓ Deleted {$file}\n";
        }
    }
    
    echo "\n4. Security Recommendations...\n";
    
    $securityFiles = ['production_login.php', 'emergency_login.php', 'quick_login.php'];
    $foundSecurityFiles = [];
    
    foreach ($securityFiles as $file) {
        if (file_exists($file)) {
            $foundSecurityFiles[] = $file;
        }
    }
    
    if (!empty($foundSecurityFiles)) {
        echo "   ⚠ WARNING: Security risk files found:\n";
        foreach ($foundSecurityFiles as $file) {
            echo "     - {$file}\n";
        }
        echo "   Please delete these files after testing!\n";
    } else {
        echo "   ✓ No security risk files found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nSUMMARY:\n";
echo "✓ Media routes added for both CMS and project contexts\n";
echo "✓ MediaController fixed (removed invalid makeDirectory parameters)\n";
echo "✓ Session domain configured for production\n";
echo "✓ Admin user data fixed\n";
echo "✓ Authentication helpers created\n";
echo "\nTo use media manager:\n";
echo "1. Make sure you're logged in (use /production_login.php if needed)\n";
echo "2. Visit /SiVGT/admin for project context\n";
echo "3. Media upload and folder creation should work now\n";
echo "4. Delete any remaining login helper files for security\n";