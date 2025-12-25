<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

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
</html>