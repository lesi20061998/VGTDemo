<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Project;

echo "🔍 Testing project login credentials...\n\n";

$projectId = 46; // Change this to your project ID
$project = Project::find($projectId);

if (!$project) {
    echo "❌ Project not found!\n";
    exit;
}

echo "📁 Project: {$project->name} (Code: {$project->code})\n";
echo "🔑 Expected Username: {$project->code}\n";
echo "🔑 Expected Password: {$project->project_admin_password}\n\n";

// Get database name
$dbName = strtolower($project->name);
$dbName = preg_replace('/[^a-z0-9_]/', '_', $dbName);
$dbName = preg_replace('/_+/', '_', $dbName);
$dbName = trim($dbName, '_');

echo "💾 Database: {$dbName}\n\n";

try {
    // Switch to project database
    DB::statement("USE `{$dbName}`");
    
    // Check if users table exists
    $tables = DB::select("SHOW TABLES LIKE 'users'");
    if (empty($tables)) {
        echo "❌ Users table doesn't exist in project database!\n";
        exit;
    }
    
    echo "✅ Users table exists\n";
    
    // Get all users
    $users = DB::select("SELECT * FROM users");
    
    if (empty($users)) {
        echo "❌ No users found in project database!\n";
        echo "🔧 Creating admin user...\n";
        
        $password = $project->project_admin_password ?: \App\Models\Project::generateProjectAdminPassword();
        $username = $project->code;
        $email = strtolower($project->code) . '@project.local';
        
        DB::statement("
            INSERT INTO users (name, username, email, password, role, level, email_verified_at, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 'cms', 2, NOW(), NOW(), NOW())
        ", [
            'CMS Admin - ' . $project->code,
            $username,
            $email,
            bcrypt($password)
        ]);
        
        // Update project with password
        DB::statement("USE " . config('database.connections.mysql.database'));
        $project->update(['project_admin_password' => $password]);
        
        echo "✅ Admin user created successfully!\n";
        echo "🔑 Username: {$username}\n";
        echo "🔑 Password: {$password}\n";
        
    } else {
        echo "✅ Found " . count($users) . " user(s):\n";
        foreach ($users as $user) {
            echo "  - ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role}\n";
        }
        
        // Test login
        $adminUser = collect($users)->where('username', $project->code)->first();
        if ($adminUser) {
            echo "\n✅ Admin user found: {$adminUser->username}\n";
            echo "🔐 Testing password...\n";
            
            if (password_verify($project->project_admin_password, $adminUser->password)) {
                echo "✅ Password matches!\n";
            } else {
                echo "❌ Password doesn't match!\n";
                echo "🔧 Updating password...\n";
                
                $newPassword = \App\Models\Project::generateProjectAdminPassword();
                DB::statement("UPDATE users SET password = ? WHERE id = ?", [
                    bcrypt($newPassword),
                    $adminUser->id
                ]);
                
                // Update project
                DB::statement("USE " . config('database.connections.mysql.database'));
                $project->update(['project_admin_password' => $newPassword]);
                
                echo "✅ Password updated: {$newPassword}\n";
            }
        } else {
            echo "❌ Admin user not found with username: {$project->code}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Login URL: " . url('/' . $project->code . '/login') . "\n";
echo "👤 Username: {$project->code}\n";
echo "🔑 Password: {$project->project_admin_password}\n";