<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class HostingerDeploymentChecklist extends Command
{
    protected $signature = 'hostinger:checklist {projectCode?} {--all}';
    protected $description = 'Complete deployment checklist for Hostinger';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        
        if ($this->option('all')) {
            return $this->checkAllProjects();
        }
        
        if ($projectCode) {
            return $this->checkSingleProject($projectCode);
        }
        
        return $this->showGeneralChecklist();
    }
    
    private function showGeneralChecklist()
    {
        $this->info("🎯 HOSTINGER DEPLOYMENT CHECKLIST");
        $this->info("==================================");
        $this->info("");
        
        $this->info("📋 PRE-DEPLOYMENT CHECKLIST:");
        $this->info("");
        
        // Environment check
        $this->checkEnvironmentSettings();
        
        // Database check
        $this->checkDatabaseConfiguration();
        
        // File permissions
        $this->checkFilePermissions();
        
        // Security settings
        $this->checkSecuritySettings();
        
        // Project databases
        $this->checkProjectDatabases();
        
        $this->info("");
        $this->info("📝 DEPLOYMENT STEPS:");
        $this->info("1. Upload files to Hostinger");
        $this->info("2. Create databases in hPanel");
        $this->info("3. Update .env file");
        $this->info("4. Run migrations");
        $this->info("5. Test project creation");
        
        return 0;
    }
    
    private function checkSingleProject($projectCode)
    {
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("❌ Project '{$projectCode}' not found!");
            return 1;
        }
        
        $this->info("🎯 DEPLOYMENT CHECKLIST FOR PROJECT: {$project->code}");
        $this->info("================================================");
        $this->info("");
        
        $this->info("📋 Project: {$project->name}");
        $this->info("🔗 Status: {$project->status}");
        $this->info("");
        
        $checklist = [
            'database_exists' => $this->checkProjectDatabase($project),
            'database_accessible' => $this->testDatabaseConnection($project),
            'tables_created' => $this->checkProjectTables($project),
            'admin_user_exists' => $this->checkAdminUser($project),
        ];
        
        $checklist['ready_for_export'] = array_reduce($checklist, function($carry, $item) {
            return $carry && $item;
        }, true);
        
        $this->info("📊 CHECKLIST RESULTS:");
        $this->info("");
        
        foreach ($checklist as $check => $status) {
            $icon = $status ? '✅' : '❌';
            $text = str_replace('_', ' ', ucwords($check, '_'));
            $this->info("   {$icon} {$text}");
        }
        
        $this->info("");
        
        if ($checklist['ready_for_export']) {
            $this->info("🎉 PROJECT IS READY FOR EXPORT!");
            $this->info("📝 Run: php artisan project:export-standalone {$projectCode}");
        } else {
            $this->warn("⚠️  PROJECT NEEDS ATTENTION BEFORE EXPORT");
            $this->info("📝 Fix the issues above and run checklist again");
        }
        
        return $checklist['ready_for_export'] ? 0 : 1;
    }
    
    private function checkAllProjects()
    {
        $projects = Project::all();
        
        $this->info("🎯 DEPLOYMENT CHECKLIST FOR ALL PROJECTS");
        $this->info("========================================");
        $this->info("");
        
        $results = [];
        
        foreach ($projects as $project) {
            $checklist = [
                'database_exists' => $this->checkProjectDatabase($project),
                'database_accessible' => $this->testDatabaseConnection($project),
                'tables_created' => $this->checkProjectTables($project),
                'admin_user_exists' => $this->checkAdminUser($project),
            ];
            
            $readyCount = array_sum($checklist);
            $totalChecks = count($checklist);
            $percentage = round(($readyCount / $totalChecks) * 100);
            
            $results[] = [
                'project' => $project,
                'checklist' => $checklist,
                'ready_count' => $readyCount,
                'total_checks' => $totalChecks,
                'percentage' => $percentage
            ];
            
            $statusIcon = $percentage === 100 ? '✅' : ($percentage >= 75 ? '⚠️' : '❌');
            $this->info("{$statusIcon} {$project->code} - {$percentage}% ready ({$readyCount}/{$totalChecks})");
        }
        
        $this->info("");
        $this->info("📊 SUMMARY:");
        
        $fullyReady = collect($results)->where('percentage', 100)->count();
        $partiallyReady = collect($results)->whereBetween('percentage', [50, 99])->count();
        $notReady = collect($results)->where('percentage', '<', 50)->count();
        
        $this->info("✅ Fully Ready: {$fullyReady}");
        $this->warn("⚠️  Partially Ready: {$partiallyReady}");
        $this->error("❌ Not Ready: {$notReady}");
        
        return 0;
    }
    
    private function checkEnvironmentSettings()
    {
        $this->info("🌍 ENVIRONMENT SETTINGS:");
        
        $checks = [
            'APP_ENV' => env('APP_ENV') === 'production',
            'APP_DEBUG' => env('APP_DEBUG') === false || env('APP_DEBUG') === 'false',
            'APP_KEY' => !empty(env('APP_KEY')),
            'APP_URL' => !str_contains(env('APP_URL', ''), 'localhost'),
        ];
        
        foreach ($checks as $setting => $status) {
            $icon = $status ? '✅' : '❌';
            $value = env($setting, 'NOT SET');
            $this->info("   {$icon} {$setting}: {$value}");
        }
    }
    
    private function checkDatabaseConfiguration()
    {
        $this->info("");
        $this->info("💾 DATABASE CONFIGURATION:");
        
        $dbUsername = env('DB_USERNAME', '');
        $dbHost = env('DB_HOST', '');
        $dbDatabase = env('DB_DATABASE', '');
        
        $checks = [
            'Hostinger Username' => preg_match('/^u\d+_/', $dbUsername),
            'Host Configuration' => !empty($dbHost),
            'Database Set' => !empty($dbDatabase),
        ];
        
        foreach ($checks as $check => $status) {
            $icon = $status ? '✅' : '❌';
            $this->info("   {$icon} {$check}");
        }
        
        if (preg_match('/^(u\d+)_/', $dbUsername, $matches)) {
            $userPrefix = $matches[1];
            $this->info("   🔑 User Prefix: {$userPrefix}");
        }
    }
    
    private function checkFilePermissions()
    {
        $this->info("");
        $this->info("📁 FILE PERMISSIONS:");
        
        $directories = [
            'storage/app',
            'storage/logs',
            'bootstrap/cache'
        ];
        
        foreach ($directories as $dir) {
            $path = base_path($dir);
            $writable = is_dir($path) && is_writable($path);
            $icon = $writable ? '✅' : '❌';
            $this->info("   {$icon} {$dir}");
        }
    }
    
    private function checkSecuritySettings()
    {
        $this->info("");
        $this->info("🔐 SECURITY SETTINGS:");
        
        $htaccessExists = File::exists(base_path('public/.htaccess'));
        $this->info("   " . ($htaccessExists ? '✅' : '❌') . " .htaccess file exists");
        
        $envExists = File::exists(base_path('.env'));
        $this->info("   " . ($envExists ? '✅' : '❌') . " .env file exists");
        
        $gitignoreExists = File::exists(base_path('.gitignore'));
        $this->info("   " . ($gitignoreExists ? '✅' : '❌') . " .gitignore file exists");
    }
    
    private function checkProjectDatabases()
    {
        $this->info("");
        $this->info("🗄️  PROJECT DATABASES:");
        
        $projects = Project::take(5)->get(); // Show first 5 projects
        
        foreach ($projects as $project) {
            $dbExists = $this->checkProjectDatabase($project);
            $icon = $dbExists ? '✅' : '❌';
            $dbName = $this->getProjectDatabaseName($project);
            $this->info("   {$icon} {$project->code} → {$dbName}");
        }
        
        $totalProjects = Project::count();
        if ($totalProjects > 5) {
            $remaining = $totalProjects - 5;
            $this->info("   ... and {$remaining} more projects");
        }
    }
    
    private function checkProjectDatabase($project)
    {
        try {
            $dbName = $this->getProjectDatabaseName($project);
            $mainDb = config('database.connections.mysql.database');
            
            DB::statement("USE `{$dbName}`");
            DB::statement("USE `{$mainDb}`");
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function testDatabaseConnection($project)
    {
        try {
            $dbName = $this->getProjectDatabaseName($project);
            $mainDb = config('database.connections.mysql.database');
            
            DB::statement("USE `{$dbName}`");
            DB::select("SELECT 1");
            DB::statement("USE `{$mainDb}`");
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkProjectTables($project)
    {
        try {
            $dbName = $this->getProjectDatabaseName($project);
            $mainDb = config('database.connections.mysql.database');
            
            DB::statement("USE `{$dbName}`");
            $tables = DB::select("SHOW TABLES");
            DB::statement("USE `{$mainDb}`");
            
            return count($tables) > 10; // Should have at least 10 tables
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkAdminUser($project)
    {
        try {
            $dbName = $this->getProjectDatabaseName($project);
            $mainDb = config('database.connections.mysql.database');
            
            DB::statement("USE `{$dbName}`");
            $users = DB::select("SELECT COUNT(*) as count FROM users WHERE role = 'cms'");
            DB::statement("USE `{$mainDb}`");
            
            return ($users[0]->count ?? 0) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkProjectPermissions($project)
    {
        try {
            // Check if project has proper permissions set
            $permissions = $project->permissions ?? collect();
            return $permissions->count() > 0;
        } catch (\Exception $e) {
            // If permissions table doesn't exist or has issues, return false
            return false;
        }
    }
    
    private function getProjectDatabaseName($project)
    {
        $code = $project->code;
        
        if (empty($code)) {
            $code = 'project_'.$project->id;
        }
        
        // HOSTINGER: Add user prefix for production
        if (app()->environment('production')) {
            $username = env('DB_USERNAME', '');
            if (preg_match('/^(u\d+)_/', $username, $matches)) {
                $userPrefix = $matches[1];
                return $userPrefix . '_' . strtolower($code);
            }
        }
        
        return 'project_'.strtolower($code);
    }
}