<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Project;

echo "🔧 Creating admin users for all projects...\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    $dbName = strtolower($project->name);
    $dbName = preg_replace('/[^a-z0-9_]/', '_', $dbName);
    $dbName = preg_replace('/_+/', '_', $dbName);
    $dbName = trim($dbName, '_');
    
    echo "📁 Processing project: {$project->name}\n";
    echo "   Database: {$dbName}\n";
    
    try {
        // Check if database exists
        $databases = DB::select("SHOW DATABASES LIKE '{$dbName}'");
        if (empty($databases)) {
            echo "   ⚠️  Database doesn't exist, skipping\n\n";
            continue;
        }
        
        DB::statement("USE `{$dbName}`");
        
        // Check if users table exists
        $tables = DB::select("SHOW TABLES LIKE 'users'");
        if (empty($tables)) {
            echo "   ⚠️  Users table doesn't exist, skipping\n\n";
            continue;
        }
        
        $password = \App\Models\Project::generateProjectAdminPassword();
        $username = $project->code;
        $email = strtolower($project->code) . '@project.local';
        
        // Insert or update admin user
        DB::statement("
            INSERT INTO users (name, username, email, password, role, level, email_verified_at, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 'cms', 2, NOW(), NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
                password = VALUES(password),
                updated_at = NOW()
        ", [
            'CMS Admin - ' . $project->code,
            $username,
            $email,
            bcrypt($password)
        ]);
        
        // Update project with credentials
        DB::statement("USE " . config('database.connections.mysql.database'));
        $project->update([
            'project_admin_username' => $username,
            'project_admin_password' => $password
        ]);
        
        echo "   ✅ Admin user created: {$username} / {$password}\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ All project admin users created!\n";