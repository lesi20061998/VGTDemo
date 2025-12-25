<?php

// Fix CSRF Token and Session Issues
echo "=== Fixing CSRF Token & Session Issues ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking Session Configuration...\n";
    
    $sessionConfig = config('session');
    echo "   Driver: " . $sessionConfig['driver'] . "\n";
    echo "   Lifetime: " . $sessionConfig['lifetime'] . " minutes\n";
    echo "   Domain: " . ($sessionConfig['domain'] ?: 'not set') . "\n";
    echo "   Path: " . $sessionConfig['path'] . "\n";
    echo "   Secure: " . ($sessionConfig['secure'] ? 'true' : 'false') . "\n";
    echo "   Same Site: " . $sessionConfig['same_site'] . "\n";
    
    // Check if session is working
    if (session()->isStarted()) {
        echo "   ✓ Session is started\n";
        echo "   Session ID: " . session()->getId() . "\n";
    } else {
        echo "   ✗ Session not started\n";
        session()->start();
        echo "   ✓ Started session manually\n";
    }
    
    echo "\n2. Checking CSRF Token...\n";
    
    $csrfToken = csrf_token();
    echo "   CSRF Token: " . ($csrfToken ?: 'EMPTY') . "\n";
    
    if ($csrfToken) {
        echo "   ✓ CSRF token generated\n";
    } else {
        echo "   ✗ CSRF token empty - regenerating...\n";
        session()->regenerateToken();
        $newToken = csrf_token();
        echo "   New CSRF Token: " . $newToken . "\n";
    }
    
    echo "\n3. Checking Database Sessions...\n";
    
    if ($sessionConfig['driver'] === 'database') {
        try {
            $sessionCount = DB::table('sessions')->count();
            echo "   Sessions in database: {$sessionCount}\n";
            
            // Clean old sessions
            $expiredSessions = DB::table('sessions')
                ->where('last_activity', '<', time() - ($sessionConfig['lifetime'] * 60))
                ->count();
                
            if ($expiredSessions > 0) {
                DB::table('sessions')
                    ->where('last_activity', '<', time() - ($sessionConfig['lifetime'] * 60))
                    ->delete();
                echo "   ✓ Cleaned {$expiredSessions} expired sessions\n";
            }
            
        } catch (Exception $e) {
            echo "   ✗ Database session error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n4. Creating CSRF-Safe Login Page...\n";
    
    $csrfSafeLogin = '<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

// Start session and generate CSRF token
if (!session()->isStarted()) {
    session()->start();
}

// Handle login
$message = "";
$messageType = "";

if ($_POST["login"] ?? false) {
    // Verify CSRF token
    $token = $_POST["_token"] ?? "";
    if (!$token || !hash_equals(session()->token(), $token)) {
        $message = "CSRF token mismatch. Please try again.";
        $messageType = "error";
        session()->regenerateToken(); // Generate new token
    } else {
        $credentials = [
            "username" => $_POST["username"],
            "password" => $_POST["password"]
        ];
        
        if (Auth::attempt($credentials, true)) {
            // Regenerate session to prevent fixation
            session()->regenerate();
            
            $message = "Login successful! Redirecting...";
            $messageType = "success";
            
            // Redirect after 2 seconds
            echo "<script>setTimeout(() => { window.location.href = \"/SiVGT/admin\"; }, 2000);</script>";
        } else {
            $message = "Invalid username or password.";
            $messageType = "error";
        }
    }
}

// Always regenerate token for security
$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html>
<head>
    <title>CSRF-Safe Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $csrfToken ?>">
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
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
        .message { padding: 10px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px; font-size: 14px; }
        .debug { margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>CSRF-Safe Login</h2>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST["username"] ?? "admin") ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" name="login" value="1">Login</button>
        </form>
        
        <div class="status">
            <strong>Current Status:</strong><br>
            <?php if (auth()->check()): ?>
                ✓ Logged in as: <?= auth()->user()->username ?><br>
                ✓ Session ID: <?= session()->getId() ?><br>
                ✓ CSRF Token: Valid<br>
                <a href="/SiVGT/admin" style="color: #007cba;">→ Go to Admin Panel</a>
            <?php else: ?>
                ✗ Not logged in<br>
                Session ID: <?= session()->getId() ?><br>
                CSRF Token: <?= substr($csrfToken, 0, 10) ?>...
            <?php endif; ?>
        </div>
        
        <div class="debug">
            <strong>Debug Info:</strong><br>
            Session Driver: <?= config("session.driver") ?><br>
            Session Domain: <?= config("session.domain") ?: "not set" ?><br>
            Session Lifetime: <?= config("session.lifetime") ?> minutes<br>
            App URL: <?= config("app.url") ?>
        </div>
    </div>
</body>
</html>';

    file_put_contents('csrf_safe_login.php', $csrfSafeLogin);
    echo "   ✓ Created csrf_safe_login.php\n";
    
    echo "\n5. Creating Session Test Page...\n";
    
    $sessionTest = '<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

// Test session functionality
if (!session()->isStarted()) {
    session()->start();
}

$testData = [
    "session_id" => session()->getId(),
    "csrf_token" => csrf_token(),
    "session_data" => session()->all(),
    "authenticated" => auth()->check(),
    "user" => auth()->user() ? [
        "id" => auth()->user()->id,
        "username" => auth()->user()->username,
        "role" => auth()->user()->role
    ] : null,
    "config" => [
        "session_driver" => config("session.driver"),
        "session_domain" => config("session.domain"),
        "session_lifetime" => config("session.lifetime"),
        "app_url" => config("app.url")
    ]
];

header("Content-Type: application/json");
echo json_encode($testData, JSON_PRETTY_PRINT);
?>';

    file_put_contents('session_test.php', $sessionTest);
    echo "   ✓ Created session_test.php\n";
    
    echo "\n6. Clearing Application Cache...\n";
    
    try {
        Artisan::call('config:clear');
        echo "   ✓ Config cache cleared\n";
        
        Artisan::call('route:clear');
        echo "   ✓ Route cache cleared\n";
        
        Artisan::call('view:clear');
        echo "   ✓ View cache cleared\n";
        
    } catch (Exception $e) {
        echo "   ✗ Cache clear error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== CSRF & Session Fix Complete ===\n";
echo "\nSTEPS TO FIX 419 CSRF ERROR:\n";
echo "1. Visit: /csrf_safe_login.php\n";
echo "2. Login with proper CSRF token handling\n";
echo "3. Test session: /session_test.php\n";
echo "4. Try media upload again\n";
echo "5. Delete test files after fixing\n";
echo "\nIf still having issues:\n";
echo "- Clear browser cookies and cache\n";
echo "- Check if SESSION_DOMAIN matches your domain\n";
echo "- Verify .env session configuration\n";