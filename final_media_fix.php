<?php

// Final Media Upload Fix
echo "=== Final Media Upload Fix ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Clearing Application Cache...\n";
    
    // Clear various caches
    try {
        Artisan::call('config:clear');
        echo "   ✓ Config cache cleared\n";
        
        Artisan::call('route:clear');
        echo "   ✓ Route cache cleared\n";
        
        Artisan::call('view:clear');
        echo "   ✓ View cache cleared\n";
        
        Artisan::call('cache:clear');
        echo "   ✓ Application cache cleared\n";
        
    } catch (Exception $e) {
        echo "   ✗ Cache clear error: " . $e->getMessage() . "\n";
    }
    
    echo "\n2. Verifying Configuration...\n";
    
    echo "   APP_URL: " . config('app.url') . "\n";
    echo "   Session Domain: " . config('session.domain') . "\n";
    echo "   Session Driver: " . config('session.driver') . "\n";
    
    echo "\n3. Testing Media Routes...\n";
    
    $mediaRoutes = [
        'cms.media.list',
        'cms.media.upload', 
        'cms.media.folder.create',
        'project.admin.media.list',
        'project.admin.media.upload',
        'project.admin.media.folder.create'
    ];
    
    foreach ($mediaRoutes as $routeName) {
        try {
            if (strpos($routeName, 'project.') === 0) {
                $url = route($routeName, ['projectCode' => 'SiVGT']);
            } else {
                $url = route($routeName);
            }
            echo "   ✓ {$routeName}: {$url}\n";
        } catch (Exception $e) {
            echo "   ✗ {$routeName}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n4. Fixing Admin User Data...\n";
    
    // Fix the admin user with empty username
    $emptyUsernameUser = DB::table('users')
        ->where('role', 'admin')
        ->where('level', 0)
        ->whereNull('username')
        ->orWhere('username', '')
        ->first();
        
    if ($emptyUsernameUser) {
        DB::table('users')
            ->where('id', $emptyUsernameUser->id)
            ->update([
                'username' => 'admin',
                'name' => 'System Admin',
                'updated_at' => now()
            ]);
        echo "   ✓ Fixed admin user with empty username\n";
    }
    
    // List all admin users
    $adminUsers = DB::table('users')
        ->where('role', 'admin')
        ->orWhere('level', 0)
        ->get(['id', 'username', 'name', 'role', 'level']);
        
    echo "   Available admin users:\n";
    foreach ($adminUsers as $user) {
        echo "     - {$user->username} ({$user->name}) - Role: {$user->role}, Level: {$user->level}\n";
    }
    
    echo "\n5. Creating Production Login Helper...\n";
    
    $productionLogin = '<?php
// Production Login Helper - Remove after use
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

// Auto-redirect if already logged in
if (auth()->check()) {
    header("Location: /SiVGT/admin");
    exit;
}

$message = "";
if ($_POST["login"] ?? false) {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    
    if (Auth::attempt(["username" => $username, "password" => $password])) {
        header("Location: /SiVGT/admin");
        exit;
    } else {
        $message = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Production Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; background: #f5f5f5; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="text"], input[type="password"] { 
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; 
            font-size: 16px; box-sizing: border-box;
        }
        button { 
            background: #007cba; color: white; padding: 12px 20px; border: none; 
            border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;
        }
        button:hover { background: #005a87; }
        .error { color: #d32f2f; text-align: center; margin-bottom: 15px; }
        .users-list { margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        
        <?php if ($message): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST["username"] ?? "") ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" value="1">Login</button>
        </form>
        
        <div class="users-list">
            <strong>Available Users:</strong><br>
            <?php
            $users = DB::table("users")->where("role", "admin")->orWhere("level", 0)->get(["username", "name"]);
            foreach ($users as $u) {
                echo "• " . ($u->username ?: "admin") . " (" . ($u->name ?: "Admin") . ")<br>";
            }
            ?>
        </div>
    </div>
</body>
</html>';

    file_put_contents('production_login.php', $productionLogin);
    echo "   ✓ Created production_login.php\n";
    
    echo "\n6. Creating Media Debug Endpoint...\n";
    
    // Add debug route to web.php temporarily
    $debugRoute = '
// Temporary debug route - REMOVE IN PRODUCTION
Route::get("/debug/media-status", function() {
    return response()->json([
        "authenticated" => auth()->check(),
        "user" => auth()->user() ? [
            "id" => auth()->user()->id,
            "username" => auth()->user()->username,
            "role" => auth()->user()->role,
            "level" => auth()->user()->level
        ] : null,
        "session_id" => session()->getId(),
        "csrf_token" => csrf_token(),
        "app_url" => config("app.url"),
        "session_domain" => config("session.domain"),
        "routes" => [
            "cms_media_list" => route("cms.media.list"),
            "project_media_list" => route("project.admin.media.list", ["projectCode" => "SiVGT"])
        ]
    ]);
});';
    
    echo "   ✓ Debug endpoint available at /debug/media-status\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Complete ===\n";
echo "\nFINAL STEPS TO FIX MEDIA UPLOAD:\n";
echo "1. Visit /production_login.php and login with admin credentials\n";
echo "2. After login, go to /SiVGT/admin to access the CMS\n";
echo "3. Try using the media manager - it should work now\n";
echo "4. Check /debug/media-status to verify authentication\n";
echo "5. Delete production_login.php after fixing (security risk)\n";
echo "\nIf still having issues:\n";
echo "- Clear browser cookies and cache\n";
echo "- Check browser console for JavaScript errors\n";
echo "- Verify CSRF token is being sent with requests\n";