<?php
// Quick Media Fix - Run this once to fix authentication issues
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Quick Media Fix ===\n\n";

// 1. Clear all caches
Artisan::call('config:clear');
Artisan::call('route:clear');
Artisan::call('cache:clear');
echo "✓ Caches cleared\n";

// 2. Fix admin user
$adminUser = DB::table('users')->where('role', 'admin')->where('level', 0)->first();
if ($adminUser && (!$adminUser->username || $adminUser->username === '')) {
    DB::table('users')->where('id', $adminUser->id)->update([
        'username' => 'admin',
        'name' => 'System Admin'
    ]);
    echo "✓ Fixed admin user\n";
}

// 3. Create simple login page
$loginPage = '<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

if ($_POST["action"] ?? "" === "login") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    
    if (Auth::attempt(["username" => $username, "password" => $password], true)) {
        echo "<script>alert(\"Login successful!\"); window.location.href = \"/SiVGT/admin\";</script>";
    } else {
        echo "<script>alert(\"Login failed!\");</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Quick Login</title></head>
<body style="font-family: Arial; max-width: 300px; margin: 50px auto; padding: 20px;">
<h3>Quick Login</h3>
<form method="POST">
    <input type="hidden" name="action" value="login">
    <p>Username: <input type="text" name="username" value="admin" style="width: 100%; padding: 5px;"></p>
    <p>Password: <input type="password" name="password" style="width: 100%; padding: 5px;"></p>
    <p><button type="submit" style="width: 100%; padding: 10px;">Login</button></p>
</form>
<p><small>Status: <?= auth()->check() ? "Logged in as " . auth()->user()->username : "Not logged in" ?></small></p>
</body>
</html>';

file_put_contents('quick_login.php', $loginPage);
echo "✓ Created quick_login.php\n";

echo "\nSTEPS:\n";
echo "1. Visit /quick_login.php\n";
echo "2. Login with username: admin, password: [your admin password]\n";
echo "3. After login, media manager should work\n";
echo "4. Delete quick_login.php after use\n";
?>