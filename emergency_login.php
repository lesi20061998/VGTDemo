<!DOCTYPE html>
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
    $app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
    
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
</html>