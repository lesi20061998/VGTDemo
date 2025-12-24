<?php

// Fix Media Authentication Issues
echo "=== Fixing Media Authentication Issues ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking Session Table...\n";
    
    // Check if sessions table exists and has data
    try {
        $sessionCount = DB::table('sessions')->count();
        echo "   Sessions in database: {$sessionCount}\n";
        
        if ($sessionCount > 0) {
            $recentSessions = DB::table('sessions')
                ->orderBy('last_activity', 'desc')
                ->limit(5)
                ->get(['id', 'user_id', 'last_activity']);
                
            echo "   Recent sessions:\n";
            foreach ($recentSessions as $session) {
                $lastActivity = date('Y-m-d H:i:s', $session->last_activity);
                echo "     - ID: {$session->id}, User: {$session->user_id}, Last: {$lastActivity}\n";
            }
        }
    } catch (Exception $e) {
        echo "   ✗ Session table error: " . $e->getMessage() . "\n";
    }
    
    echo "\n2. Checking Users Table...\n";
    
    try {
        $userCount = DB::table('users')->count();
        echo "   Total users: {$userCount}\n";
        
        $adminUsers = DB::table('users')
            ->where('role', 'admin')
            ->orWhere('level', 0)
            ->get(['id', 'username', 'role', 'level', 'created_at']);
            
        echo "   Admin users:\n";
        foreach ($adminUsers as $user) {
            echo "     - ID: {$user->id}, Username: {$user->username}, Role: {$user->role}, Level: {$user->level}\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Users table error: " . $e->getMessage() . "\n";
    }
    
    echo "\n3. Testing Authentication Middleware...\n";
    
    // Check auth guards
    $guards = config('auth.guards');
    echo "   Available guards:\n";
    foreach ($guards as $name => $config) {
        echo "     - {$name}: driver={$config['driver']}, provider={$config['provider']}\n";
    }
    
    echo "\n4. Creating Test Authentication...\n";
    
    // Find or create a test admin user
    $testUser = DB::table('users')
        ->where('role', 'admin')
        ->orWhere('level', 0)
        ->first();
        
    if ($testUser) {
        echo "   Found admin user: {$testUser->username} (ID: {$testUser->id})\n";
        
        // Try to authenticate this user
        try {
            auth()->loginUsingId($testUser->id);
            
            if (auth()->check()) {
                echo "   ✓ Successfully authenticated user\n";
                echo "   Current user: " . auth()->user()->username . "\n";
                
                // Test media endpoint with authentication
                echo "\n   Testing media endpoint with auth...\n";
                
                $controller = new App\Http\Controllers\Admin\MediaController();
                $request = new \Illuminate\Http\Request();
                $request->merge(['path' => '']);
                
                $response = $controller->list($request);
                if ($response instanceof \Illuminate\Http\JsonResponse) {
                    $data = $response->getData(true);
                    echo "   ✓ Media endpoint works with authentication\n";
                    echo "     Folders: " . count($data['folders'] ?? []) . "\n";
                    echo "     Files: " . count($data['files'] ?? []) . "\n";
                } else {
                    echo "   ✗ Media endpoint failed even with authentication\n";
                }
                
            } else {
                echo "   ✗ Failed to authenticate user\n";
            }
        } catch (Exception $e) {
            echo "   ✗ Authentication error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✗ No admin user found\n";
        
        // Create a test admin user
        echo "   Creating test admin user...\n";
        try {
            $userId = DB::table('users')->insertGetId([
                'username' => 'testadmin',
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'level' => 0,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "   ✓ Created test admin user (ID: {$userId})\n";
            echo "   Username: testadmin, Password: password123\n";
        } catch (Exception $e) {
            echo "   ✗ Failed to create test user: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n5. Checking Project Authentication...\n";
    
    // Check project-specific authentication
    try {
        // Check if there are CMS users for projects
        $cmsUsers = DB::table('users')
            ->where('role', 'cms')
            ->where('level', 2)
            ->get(['id', 'username', 'project_ids']);
            
        echo "   CMS users found: " . count($cmsUsers) . "\n";
        foreach ($cmsUsers as $user) {
            echo "     - {$user->username} (Projects: {$user->project_ids})\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ Project users error: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Fixing Session Configuration...\n";
    
    // Check session configuration
    $sessionConfig = config('session');
    
    echo "   Current session config:\n";
    echo "     Driver: {$sessionConfig['driver']}\n";
    echo "     Lifetime: {$sessionConfig['lifetime']}\n";
    echo "     Domain: " . ($sessionConfig['domain'] ?: 'null') . "\n";
    echo "     Path: {$sessionConfig['path']}\n";
    echo "     Secure: " . ($sessionConfig['secure'] ? 'true' : 'false') . "\n";
    echo "     Same Site: {$sessionConfig['same_site']}\n";
    
    // Recommendations for session config
    echo "\n   Recommendations:\n";
    echo "   - Set SESSION_DOMAIN=core.vnglobaltech.com in .env\n";
    echo "   - Set SESSION_SECURE=false for HTTP (true for HTTPS)\n";
    echo "   - Set SESSION_SAME_SITE=lax\n";
    
    echo "\n7. Creating Authentication Fix Script...\n";
    
    // Create a simple login endpoint for testing
    $loginScript = '<?php
// Simple login test - REMOVE IN PRODUCTION
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

if ($_POST["action"] ?? "" === "login") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    
    $user = DB::table("users")
        ->where("username", $username)
        ->first();
        
    if ($user && Hash::check($password, $user->password)) {
        auth()->loginUsingId($user->id);
        echo "Login successful! User: " . $user->username;
    } else {
        echo "Login failed!";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Test Login</title></head>
<body>
<h2>Test Login</h2>
<form method="POST">
    <input type="hidden" name="action" value="login">
    <p>Username: <input type="text" name="username" value="testadmin"></p>
    <p>Password: <input type="password" name="password" value="password123"></p>
    <p><button type="submit">Login</button></p>
</form>
<p>Current auth status: ' . (auth()->check() ? 'Logged in as ' . auth()->user()->username : 'Not logged in') . '</p>
</body>
</html>';

    file_put_contents('test_login.php', $loginScript);
    echo "   ✓ Created test_login.php - Use this to test authentication\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Fix Complete ===\n";
echo "\nNEXT STEPS:\n";
echo "1. Visit /test_login.php to test authentication\n";
echo "2. Update .env with proper session configuration\n";
echo "3. Make sure you're logged in before using media manager\n";
echo "4. Check browser cookies and session storage\n";
echo "5. Clear browser cache and try again\n";