<?php

// Reset All User Passwords to "1"
echo "=== Resetting All User Passwords ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking Database Connection...\n";
    
    try {
        DB::connection()->getPdo();
        echo "   ✓ Database connected successfully\n";
    } catch (Exception $e) {
        echo "   ✗ Database error: " . $e->getMessage() . "\n";
        exit;
    }
    
    echo "\n2. Getting All Users...\n";
    
    $users = DB::table('users')->get();
    echo "   Found " . count($users) . " users\n";
    
    if (count($users) === 0) {
        echo "   ✗ No users found in database\n";
        exit;
    }
    
    echo "\n3. Resetting Passwords...\n";
    
    $newPassword = '1';
    $hashedPassword = bcrypt($newPassword);
    
    foreach ($users as $user) {
        try {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'password' => $hashedPassword,
                    'updated_at' => now()
                ]);
                
            $username = $user->username ?: $user->email ?: "ID:{$user->id}";
            $role = $user->role ?: 'user';
            $level = isset($user->level) ? " (Level: {$user->level})" : '';
            
            echo "   ✓ Reset password for: {$username} [{$role}]{$level}\n";
            
        } catch (Exception $e) {
            echo "   ✗ Failed to reset password for user ID {$user->id}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n4. Creating Quick Login Credentials List...\n";
    
    $loginList = "=== LOCAL DEVELOPMENT LOGIN CREDENTIALS ===\n\n";
    $loginList .= "All passwords have been reset to: 1\n\n";
    $loginList .= "Available Users:\n";
    $loginList .= str_repeat("-", 50) . "\n";
    
    $users = DB::table('users')->orderBy('level', 'asc')->orderBy('role', 'asc')->get();
    
    foreach ($users as $user) {
        $username = $user->username ?: $user->email ?: "ID:{$user->id}";
        $role = $user->role ?: 'user';
        $level = isset($user->level) ? $user->level : 'N/A';
        $name = $user->name ?: 'No name';
        
        $loginList .= sprintf("Username: %-15s | Role: %-10s | Level: %-3s | Name: %s\n", 
            $username, $role, $level, $name);
    }
    
    $loginList .= str_repeat("-", 50) . "\n";
    $loginList .= "\nQuick Access:\n";
    $loginList .= "- Super Admin (Level 0): Use any admin user with level 0\n";
    $loginList .= "- Regular Admin: Use any admin user with level > 0\n";
    $loginList .= "- CMS User: Use any user with cms role\n";
    $loginList .= "\nLogin URLs:\n";
    $loginList .= "- Main Login: http://localhost:8000/login\n";
    $loginList .= "- CSRF Safe Login: http://localhost:8000/csrf_safe_login.php\n";
    $loginList .= "- Project Admin: http://localhost:8000/SiVGT/admin\n";
    $loginList .= "- Super Admin: http://localhost:8000/superadmin\n";
    
    file_put_contents('LOCAL_CREDENTIALS.txt', $loginList);
    echo "   ✓ Created LOCAL_CREDENTIALS.txt\n";
    
    echo "\n5. Testing Login with Reset Password...\n";
    
    // Find first admin user and test login
    $adminUser = DB::table('users')
        ->where('role', 'admin')
        ->orderBy('level', 'asc')
        ->first();
        
    if ($adminUser) {
        $credentials = [
            'username' => $adminUser->username ?: $adminUser->email,
            'password' => $newPassword
        ];
        
        if (Auth::attempt($credentials)) {
            echo "   ✓ Login test successful with: " . $credentials['username'] . "\n";
            echo "   ✓ Authenticated as: " . auth()->user()->username . " (Role: " . auth()->user()->role . ")\n";
        } else {
            echo "   ✗ Login test failed\n";
        }
    }
    
    echo "\n6. Creating Simple Login Page...\n";
    
    $simpleLogin = '<!DOCTYPE html>
<html>
<head>
    <title>Simple Local Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; background: #f0f0f0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; width: 100%; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
        .quick-users { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .quick-users h4 { margin-top: 0; }
        .user-btn { background: #007bff; color: white; border: none; padding: 8px 12px; margin: 2px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .user-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔑 Local Development Login</h2>
        
        <div class="info">
            <strong>All passwords reset to:</strong> <code>1</code><br>
            <strong>Environment:</strong> Local Development
        </div>
        
        <div class="quick-users">
            <h4>Quick Login:</h4>';
            
    // Add quick login buttons for common users
    $quickUsers = DB::table('users')
        ->whereIn('role', ['admin', 'cms'])
        ->orderBy('level', 'asc')
        ->limit(5)
        ->get();
        
    foreach ($quickUsers as $user) {
        $username = $user->username ?: $user->email;
        $role = $user->role;
        $level = isset($user->level) ? " L{$user->level}" : '';
        
        $simpleLogin .= "<button type=\"button\" class=\"user-btn\" onclick=\"quickLogin('{$username}')\">{$username} ({$role}{$level})</button> ";
    }
    
    $simpleLogin .= '
        </div>
        
        <form method="POST" action="/login">
            <input type="hidden" name="_token" value="' . csrf_token() . '">
            
            <div class="form-group">
                <label for="username">Username/Email:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="1" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #666;">
            <a href="/superadmin">Super Admin</a> | 
            <a href="/SiVGT/admin">Project Admin</a> | 
            <a href="/admin">CMS Admin</a>
        </div>
    </div>
    
    <script>
        function quickLogin(username) {
            document.getElementById("username").value = username;
            document.getElementById("password").value = "1";
        }
    </script>
</body>
</html>';

    file_put_contents('simple_login.php', $simpleLogin);
    echo "   ✓ Created simple_login.php\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Password Reset Complete ===\n";
echo "\n🎉 ALL PASSWORDS RESET TO: 1\n";
echo "\nQUICK ACCESS:\n";
echo "1. Visit: http://localhost:8000/simple_login.php\n";
echo "2. Use any username with password: 1\n";
echo "3. Check LOCAL_CREDENTIALS.txt for full user list\n";
echo "\nCommon URLs:\n";
echo "- Super Admin: http://localhost:8000/superadmin\n";
echo "- Project Admin: http://localhost:8000/SiVGT/admin\n";
echo "- Media Test: http://localhost:8000/test_media_local.html\n";
echo "\n⚠️  Remember: This is for LOCAL DEVELOPMENT ONLY!\n";