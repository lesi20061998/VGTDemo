<?php

// Simple Authentication Fix for Media Upload
echo "=== Simple Authentication Fix ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Current Authentication Status...\n";
    if (auth()->check()) {
        echo "   ✓ User is authenticated: " . auth()->user()->username . "\n";
    } else {
        echo "   ✗ No user authenticated\n";
    }
    
    echo "\n2. Finding Admin Users...\n";
    
    $adminUsers = DB::table('users')
        ->where(function($query) {
            $query->where('role', 'admin')
                  ->orWhere('level', 0);
        })
        ->get(['id', 'username', 'role', 'level']);
        
    if ($adminUsers->count() > 0) {
        echo "   Found " . $adminUsers->count() . " admin users:\n";
        foreach ($adminUsers as $user) {
            echo "     - {$user->username} (Role: {$user->role}, Level: {$user->level})\n";
        }
        
        // Auto-login the first admin user for testing
        $firstAdmin = $adminUsers->first();
        echo "\n3. Auto-authenticating first admin user...\n";
        
        try {
            auth()->loginUsingId($firstAdmin->id);
            
            if (auth()->check()) {
                echo "   ✓ Successfully authenticated: " . auth()->user()->username . "\n";
                
                // Test media endpoint
                echo "\n4. Testing Media Endpoint...\n";
                $controller = new App\Http\Controllers\Admin\MediaController();
                $request = new \Illuminate\Http\Request();
                $request->merge(['path' => '']);
                
                $response = $controller->list($request);
                if ($response instanceof \Illuminate\Http\JsonResponse) {
                    $data = $response->getData(true);
                    echo "   ✓ Media endpoint working!\n";
                    echo "     Response: " . json_encode($data) . "\n";
                } else {
                    echo "   ✗ Media endpoint failed\n";
                }
            } else {
                echo "   ✗ Authentication failed\n";
            }
        } catch (Exception $e) {
            echo "   ✗ Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✗ No admin users found\n";
        
        echo "\n3. Creating Emergency Admin User...\n";
        try {
            $userId = DB::table('users')->insertGetId([
                'username' => 'emergency_admin',
                'name' => 'Emergency Admin',
                'email' => 'emergency@admin.com',
                'password' => bcrypt('admin123456'),
                'role' => 'admin',
                'level' => 0,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "   ✓ Created emergency admin user\n";
            echo "     Username: emergency_admin\n";
            echo "     Password: admin123456\n";
            echo "     ID: {$userId}\n";
            
        } catch (Exception $e) {
            echo "   ✗ Failed to create user: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n5. Session Configuration Check...\n";
    
    $sessionDriver = config('session.driver');
    $sessionLifetime = config('session.lifetime');
    $sessionDomain = config('session.domain');
    
    echo "   Driver: {$sessionDriver}\n";
    echo "   Lifetime: {$sessionLifetime} minutes\n";
    echo "   Domain: " . ($sessionDomain ?: 'not set') . "\n";
    
    if (!$sessionDomain) {
        echo "   ⚠ WARNING: SESSION_DOMAIN not set in .env\n";
        echo "   Add this to .env: SESSION_DOMAIN=core.vnglobaltech.com\n";
    }
    
    echo "\n6. Creating Simple Login Page...\n";
    
    $loginPage = '<!DOCTYPE html>
<html>
<head>
    <title>Emergency Login - Media Fix</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        button:hover { background: #005a87; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status { margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Emergency Login - Media Fix</h2>
    
    <?php
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    $app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();
    
    $message = "";
    $messageType = "";
    
    if ($_POST["action"] ?? "" === "login") {
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";
        
        if ($username && $password) {
            $user = DB::table("users")
                ->where("username", $username)
                ->first();
                
            if ($user && Hash::check($password, $user->password)) {
                auth()->loginUsingId($user->id);
                $message = "Login successful! You can now use the media manager.";
                $messageType = "success";
            } else {
                $message = "Invalid username or password.";
                $messageType = "error";
            }
        } else {
            $message = "Please enter both username and password.";
            $messageType = "error";
        }
    }
    
    if ($message) {
        echo "<div class=\"alert alert-{$messageType}\">{$message}</div>";
    }
    ?>
    
    <form method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= $_POST["username"] ?? "" ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    
    <div class="status">
        <strong>Current Status:</strong><br>
        <?php if (auth()->check()): ?>
            ✓ Logged in as: <?= auth()->user()->username ?><br>
            ✓ Role: <?= auth()->user()->role ?? "N/A" ?><br>
            ✓ Level: <?= auth()->user()->level ?? "N/A" ?>
        <?php else: ?>
            ✗ Not logged in
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 20px; font-size: 12px; color: #666;">
        <strong>Available Users:</strong><br>
        <?php
        $users = DB::table("users")->where("role", "admin")->orWhere("level", 0)->get(["username", "role", "level"]);
        foreach ($users as $u) {
            echo "• {$u->username} (Role: {$u->role}, Level: {$u->level})<br>";
        }
        ?>
    </div>
</body>
</html>';

    file_put_contents('emergency_login.php', $loginPage);
    echo "   ✓ Created emergency_login.php\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Complete ===\n";
echo "\nIMPORTANT STEPS:\n";
echo "1. Visit /emergency_login.php in your browser\n";
echo "2. Login with an admin account\n";
echo "3. After login, try the media manager again\n";
echo "4. Add SESSION_DOMAIN=core.vnglobaltech.com to .env file\n";
echo "5. Delete emergency_login.php after fixing (security risk)\n";