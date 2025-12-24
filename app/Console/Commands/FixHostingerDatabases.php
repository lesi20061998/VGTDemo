<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class FixHostingerDatabases extends Command
{
    protected $signature = 'hostinger:fix-databases {--check : Only check database names}';
    protected $description = 'Fix database names for Hostinger hosting';

    public function handle()
    {
        $checkOnly = $this->option('check');
        
        $this->info("🔧 Fixing Hostinger database names...");
        
        $projects = Project::all();
        $userPrefix = $this->getUserPrefix();
        
        $this->info("User prefix detected: {$userPrefix}");
        $this->info("Found {$projects->count()} projects to check");
        
        foreach ($projects as $project) {
            $this->processProject($project, $userPrefix, $checkOnly);
        }
        
        if ($checkOnly) {
            $this->info("\n✅ Check completed. Use without --check to actually fix databases.");
        } else {
            $this->info("\n✅ Database fix completed!");
        }
    }
    
    private function getUserPrefix()
    {
        $username = env('DB_USERNAME', 'root');
        
        // Extract user prefix from Hostinger username (e.g., u712054581_VGTApp -> u712054581)
        if (preg_match('/^(u\d+)_/', $username, $matches)) {
            return $matches[1];
        }
        
        return 'u712054581'; // Default fallback
    }
    
    private function processProject($project, $userPrefix, $checkOnly)
    {
        $originalDbName = 'project_' . strtolower($project->code);
        $hostingerDbName = $userPrefix . '_' . strtolower($project->code);
        
        $this->info("\n📋 Project: {$project->code}");
        $this->info("  Original DB: {$originalDbName}");
        $this->info("  Hostinger DB: {$hostingerDbName}");
        
        if ($checkOnly) {
            $this->checkDatabase($hostingerDbName, $project);
        } else {
            $this->fixDatabase($originalDbName, $hostingerDbName, $project);
        }
    }
    
    private function checkDatabase($dbName, $project)
    {
        try {
            // Test connection
            $testConnection = [
                'driver' => 'mysql',
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => $dbName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ];
            
            config(['database.connections.test_db' => $testConnection]);
            DB::connection('test_db')->getPdo();
            
            $this->info("  ✅ Database exists and accessible");
            
            // Check tables
            $tables = DB::connection('test_db')->select('SHOW TABLES');
            $this->info("  📊 Tables: " . count($tables));
            
        } catch (\Exception $e) {
            $this->error("  ❌ Database not accessible: " . $e->getMessage());
            $this->warn("  💡 You need to create database '{$dbName}' in Hostinger hPanel");
            $this->warn("  💡 And assign user permissions to this database");
        }
    }
    
    private function fixDatabase($originalDbName, $hostingerDbName, $project)
    {
        $this->info("  🔧 Fixing database connection...");
        
        try {
            // Test new database name
            $testConnection = [
                'driver' => 'mysql',
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => $hostingerDbName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ];
            
            config(['database.connections.test_db' => $testConnection]);
            DB::connection('test_db')->getPdo();
            
            $this->info("  ✅ Successfully connected to {$hostingerDbName}");
            
            // Update project if needed
            if ($project->database_name !== $hostingerDbName) {
                $project->update(['database_name' => $hostingerDbName]);
                $this->info("  📝 Updated project database name");
            }
            
        } catch (\Exception $e) {
            $this->error("  ❌ Cannot connect to {$hostingerDbName}: " . $e->getMessage());
            
            $this->warn("  📋 Manual steps required:");
            $this->warn("     1. Login to Hostinger hPanel");
            $this->warn("     2. Go to Databases → MySQL Databases");
            $this->warn("     3. Create database: {$hostingerDbName}");
            $this->warn("     4. Assign user: " . env('DB_USERNAME') . " with ALL PRIVILEGES");
            $this->warn("     5. Run this command again");
        }
    }
}