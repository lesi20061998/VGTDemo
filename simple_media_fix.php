<?php
// Simple Media Fix - No symlink needed
echo "=== Simple Media Fix ===\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// 1. Create public/storage directory
echo "1. Creating storage directory...\n";
$publicStorage = public_path('storage');
if (!is_dir($publicStorage)) {
    mkdir($publicStorage, 0755, true);
    echo "   ✓ Created public/storage directory\n";
} else {
    echo "   ✓ Directory already exists\n";
}

// 2. Auto-login admin
echo "2. Finding admin user...\n";
$admin = DB::table('users')->where('role', 'admin')->where('level', 0)->first();
if ($admin) {
    auth()->loginUsingId($admin->id);
    echo "   ✓ Auto-logged in: " . ($admin->username ?: 'admin') . "\n";
} else {
    echo "   ✗ No admin found\n";
}

// 3. Create simple login page
echo "3. Creating login page...\n";
$login = '<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

if ($_POST["go"] ?? false) {
    if (Auth::attempt(["username" => $_POST["u"], "password" => $_POST["p"]], true)) {
        echo "<script>alert(\"Success!\"); window.location=\"/SiVGT/admin\";</script>";
    } else {
        echo "<script>alert(\"Failed!\");</script>";
    }
}
?>
<html><head><title>Login</title></head>
<body style="font-family:Arial;max-width:250px;margin:50px auto;">
<h3>Media Fix Login</h3>
<form method="POST">
<p>User: <input name="u" value="admin" style="width:100%;padding:5px;"></p>
<p>Pass: <input name="p" type="password" style="width:100%;padding:5px;"></p>
<p><button name="go" value="1" style="width:100%;padding:8px;">GO</button></p>
</form>
<p><small><?= auth()->check() ? "✓ " . auth()->user()->username : "✗ Not logged" ?></small></p>
</body></html>';

file_put_contents('go.php', $login);
echo "   ✓ Created go.php\n";

echo "\n=== DONE ===\n";
echo "1. Visit: https://core.vnglobaltech.com/go.php\n";
echo "2. Login with admin password\n";
echo "3. Media should work now!\n";
echo "4. Delete go.php after use\n";
?>