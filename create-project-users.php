<?php

/**
 * Script để tạo user trong project database
 * Usage: php create-project-users.php [project_code]
 */

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$projectCode = $argv[1] ?? 'sivgt';
$projectDbName = 'project_'.strtolower($projectCode);

echo "Creating user in project database: {$projectDbName}\n";

// Set project database connection
Config::set('database.connections.project.database', $projectDbName);
DB::purge('project');

try {
    // Check if users table exists
    $tables = DB::connection('project')->select('SHOW TABLES');
    $tableNames = array_map(fn ($t) => array_values((array) $t)[0], $tables);

    if (! in_array('users', $tableNames)) {
        echo "ERROR: users table does not exist in {$projectDbName}\n";
        echo 'Available tables: '.implode(', ', $tableNames)."\n";
        exit(1);
    }

    // Check if user already exists
    $existingUser = DB::connection('project')
        ->table('users')
        ->where('username', $projectCode)
        ->orWhere('username', strtoupper($projectCode))
        ->first();

    if ($existingUser) {
        echo "User already exists: {$existingUser->username}\n";

        // Update password to '1'
        DB::connection('project')
            ->table('users')
            ->where('id', $existingUser->id)
            ->update(['password' => Hash::make('1')]);

        echo "Password updated to: 1\n";
    } else {
        // Create new user
        $userId = DB::connection('project')->table('users')->insertGetId([
            'name' => 'Admin - '.strtoupper($projectCode),
            'username' => strtoupper($projectCode),
            'email' => strtolower($projectCode).'@project.local',
            'password' => Hash::make('1'),
            'role' => 'cms',
            'level' => 2,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Created new user with ID: {$userId}\n";
        echo 'Username: '.strtoupper($projectCode)."\n";
        echo "Password: 1\n";
    }

    // List all users
    echo "\nAll users in {$projectDbName}:\n";
    $users = DB::connection('project')->table('users')->get(['id', 'username', 'email', 'role']);
    foreach ($users as $user) {
        echo "- ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role}\n";
    }

} catch (\Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    exit(1);
}

echo "\nDone!\n";
