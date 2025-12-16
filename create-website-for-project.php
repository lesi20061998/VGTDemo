<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use Illuminate\Support\Facades\DB;

$projectId = 46;
$project = Project::find($projectId);

if (!$project) {
    echo "❌ Project not found!\n";
    exit;
}

echo "🚀 Creating website for project: {$project->name} (Code: {$project->code})\n\n";

if ($project->status !== 'assigned') {
    echo "⚠️  Project status is '{$project->status}', should be 'assigned'\n";
    echo "🔧 Updating status to 'assigned'...\n";
    $project->update(['status' => 'assigned']);
}

try {
    $dbName = strtolower($project->name);
    $dbName = preg_replace('/[^a-z0-9_]/', '_', $dbName);
    $dbName = preg_replace('/_+/', '_', $dbName);
    $dbName = trim($dbName, '_');
    
    echo "💾 Creating database: {$dbName}\n";
    
    DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    DB::statement("USE `{$dbName}`");
    
    echo "📋 Creating CMS tables...\n";
    
    $mainDb = config('database.connections.mysql.database');
    $tables = [
        'users', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
        'products_enhanced', 'product_categories', 'brands', 'product_attributes', 'product_attribute_values',
        'product_attribute_value_mappings', 'product_variations', 'menus', 'menu_items',
        'orders', 'order_items', 'banners', 'contact_forms', 'visitor_logs', 'media', 'migrations'
    ];
    
    foreach ($tables as $tableName) {
        try {
            $result = DB::select("SHOW CREATE TABLE `{$mainDb}`.`{$tableName}`");
            if (!empty($result)) {
                $sql = $result[0]->{'Create Table'};
                $sql = preg_replace('/,\s*CONSTRAINT\s+`[^`]+_tenant_id_foreign`[^,]+/', '', $sql);
                $sql = preg_replace('/,\s*CONSTRAINT\s+`[^`]+`\s+FOREIGN KEY[^,)]+/', '', $sql);
                $sql = str_replace("CREATE TABLE `{$tableName}`", "CREATE TABLE IF NOT EXISTS `{$tableName}`", $sql);
                DB::statement($sql);
                echo "  ✅ {$tableName}\n";
            }
        } catch (\Exception $e) {
            echo "  ⚠️  {$tableName} - " . substr($e->getMessage(), 0, 80) . "\n";
        }
    }
    
    echo "👤 Creating admin user...\n";
    
    $password = \App\Models\Project::generateProjectAdminPassword();
    $username = $project->code;
    $email = strtolower($project->code) . '@project.local';
    
    DB::statement("
        INSERT INTO users (name, username, email, password, role, level, email_verified_at, created_at, updated_at) 
        VALUES (?, ?, ?, ?, 'cms', 2, NOW(), NOW(), NOW())
        ON DUPLICATE KEY UPDATE password = VALUES(password)
    ", [
        'CMS Admin - ' . $project->code,
        $username,
        $email,
        bcrypt($password)
    ]);
    
    DB::statement("USE " . config('database.connections.mysql.database'));
    
    $project->update([
        'project_admin_username' => $username,
        'project_admin_password' => $password,
        'status' => 'active',
        'initialized_at' => now(),
    ]);
    
    echo "✅ Website created successfully!\n\n";
    echo "🎯 Login URL: " . url('/' . $project->code . '/login') . "\n";
    echo "👤 Username: {$username}\n";
    echo "🔑 Password: {$password}\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
