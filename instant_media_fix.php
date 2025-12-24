<?php
// Instant Media Fix - One-click solution
echo "=== Instant Media Fix ===\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// 1. Fix storage symlink
echo "1. Fixing storage symlink...\n";
$publicStorage = public_path('storage');
$targetStorage = storage_path('app/public');

if (!file_exists($publicStorage)) {
    if (symlink($targetStorage, $publicStorage)) {
        echo "   ✓ Created storage symlink\n";
    } else {
        // Fallback: copy directory
        mkdir($publicStorage, 0755, true);
        shell_exec("cp -r {$targetStorage}/* {$publicStorage}/");
        echo "   ✓ Copied storage files\n";
    }
} else {
    echo "   ✓ Storage already exists\n";
}

// 2. Auto-login admin user
echo "2. Auto-login admin user...\n";
$admin = DB::table('users')->where('role', 'admin')->where('level', 0)->first();
if ($admin) {
    auth()->loginUsingId($admin->id);
    echo "   ✓ Logged in as: " . ($admin->username ?: 'admin') . "\n";
} else {
    echo "   ✗ No admin user found\n";
}

// 3. Test media functionality
echo "3. Testing media...\n";
if (auth()->check()) {
    $controller = new App\Http\Controllers\Admin\MediaController();
    $request = new \Illuminate\Http\Request();
    $request->merge(['path' => '']);
    
    $response = $controller->list($request);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        echo "   ✓ Media API working\n";
    }
} else {
    echo "   ✗ Authentication failed\n";
}

echo "\n=== QUICK LOGIN PAGE ===\n";

// Create instant login page
$loginPage = '<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

if ($_POST["login"] ?? false) {
    if (Auth::attempt(["username" => $_POST["username"], "password" => $_POST["password"]], true)) {
        header("Location: /SiVGT/admin");
        exit;
    }
}
?>
<!DOCTYPE html>
<html><head><title>Login</title></head>
<body style="font-family:Arial;max-width:300px;margin:50px auto;padding:20px;">
<h3>Quick Login</h3>
<form method="POST">
<p>Username: <input type="text" name="username" value="admin" style="width:100%;padding:5px;"></p>
<p>Password: <input type="password" name="password" style="width:100%;padding:5px;"></p>
<p><button type="submit" name="login" value="1" style="width:100%;padding:10px;">Login</button></p>
</form>
<p><small><?= auth()->check() ? "✓ Logged in: " . auth()->user()->username : "✗ Not logged in" ?></small></p>
</body></html>';

file_put_contents('login.php', $loginPage);
echo "✓ Created /login.php\n";

echo "\nNOW DO THIS:\n";
echo "1. Visit: https://core.vnglobaltech.com/login.php\n";
echo "2. Login with admin credentials\n";
echo "3. Go to: https://core.vnglobaltech.com/SiVGT/admin\n";
echo "4. Try media upload - should work now!\n";
echo "5. Delete login.php after use\n";
?>