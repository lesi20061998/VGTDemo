<?php

// Setup Local Development Environment
echo "=== Setting up Local Development ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Clearing All Caches...\n";
    
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
    
    echo "\n2. Checking Local Configuration...\n";
    
    echo "   Environment: " . app()->environment() . "\n";
    echo "   Debug Mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
    echo "   APP_URL: " . config('app.url') . "\n";
    echo "   Session Domain: " . (config('session.domain') ?: 'localhost (default)') . "\n";
    echo "   Database: " . config('database.default') . "\n";
    
    echo "\n3. Testing Database Connection...\n";
    
    try {
        DB::connection()->getPdo();
        echo "   ✓ Database connected successfully\n";
        
        $userCount = DB::table('users')->count();
        echo "   ✓ Found {$userCount} users in database\n";
        
    } catch (Exception $e) {
        echo "   ✗ Database error: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Setting up Storage...\n";
    
    // Create storage directories
    $storageDirs = [
        'storage/app/public',
        'storage/app/public/media',
        'public/storage'
    ];
    
    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "   ✓ Created {$dir}\n";
        } else {
            echo "   ✓ {$dir} exists\n";
        }
    }
    
    // Copy storage files to public (for local development)
    $sourceDir = 'storage/app/public';
    $targetDir = 'public/storage';
    
    if (is_dir($sourceDir) && is_dir($targetDir)) {
        // Simple copy function
        function copyFiles($src, $dst) {
            if (is_dir($src)) {
                $files = scandir($src);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        $srcFile = $src . '/' . $file;
                        $dstFile = $dst . '/' . $file;
                        
                        if (is_dir($srcFile)) {
                            if (!is_dir($dstFile)) {
                                mkdir($dstFile, 0755, true);
                            }
                            copyFiles($srcFile, $dstFile);
                        } else {
                            copy($srcFile, $dstFile);
                        }
                    }
                }
            }
        }
        
        copyFiles($sourceDir, $targetDir);
        echo "   ✓ Copied storage files to public\n";
    }
    
    echo "\n5. Creating Local Admin User...\n";
    
    // Check if admin user exists
    $adminUser = DB::table('users')
        ->where('role', 'admin')
        ->where('level', 0)
        ->first();
        
    if (!$adminUser) {
        // Create admin user for local development
        $userId = DB::table('users')->insertGetId([
            'username' => 'admin',
            'name' => 'Local Admin',
            'email' => 'admin@localhost.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'level' => 0,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "   ✓ Created local admin user\n";
        echo "     Username: admin\n";
        echo "     Password: admin123\n";
    } else {
        echo "   ✓ Admin user already exists: " . ($adminUser->username ?: 'admin') . "\n";
    }
    
    echo "\n6. Testing Routes...\n";
    
    $testRoutes = [
        'login' => 'login',
        'cms.media.list' => 'admin/media/list',
        'project.admin.media.list' => 'SiVGT/admin/media/list'
    ];
    
    foreach ($testRoutes as $name => $expectedUri) {
        try {
            if ($name === 'project.admin.media.list') {
                $url = route($name, ['projectCode' => 'SiVGT']);
            } else {
                $url = route($name);
            }
            echo "   ✓ {$name}: {$url}\n";
        } catch (Exception $e) {
            echo "   ✗ {$name}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n7. Creating Local Login Page...\n";
    
    $localLogin = '<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

if ($_POST["login"] ?? false) {
    $credentials = [
        "username" => $_POST["username"],
        "password" => $_POST["password"]
    ];
    
    if (Auth::attempt($credentials, true)) {
        header("Location: /SiVGT/admin");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Local Development Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 350px; margin: 100px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #007cba; color: white; padding: 12px; border: none; border-radius: 4px; width: 100%; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <h2>Local Development Login</h2>
    
    <div class="info">
        <strong>Local Admin Credentials:</strong><br>
        Username: <code>admin</code><br>
        Password: <code>admin123</code>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" value="admin" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" value="admin123" required>
        </div>
        <button type="submit" name="login" value="1">Login to Local CMS</button>
    </form>
    
    <div style="margin-top: 20px; font-size: 12px; color: #666;">
        <strong>Status:</strong> 
        <?= auth()->check() ? "✓ Logged in as " . auth()->user()->username : "✗ Not logged in" ?>
    </div>
</body>
</html>';

    file_put_contents('local_login.php', $localLogin);
    echo "   ✓ Created local_login.php\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Local Setup Complete ===\n";
echo "\nLOCAL DEVELOPMENT READY:\n";
echo "1. Start server: php artisan serve\n";
echo "2. Visit: http://localhost:8000/local_login.php\n";
echo "3. Login with: admin / admin123\n";
echo "4. Access CMS: http://localhost:8000/SiVGT/admin\n";
echo "5. Test media upload and other features\n";
echo "\nLocal admin credentials:\n";
echo "- Username: admin\n";
echo "- Password: admin123\n";
echo "\nDelete local_login.php when done testing.\n";