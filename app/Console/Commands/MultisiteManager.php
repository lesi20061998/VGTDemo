<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class MultisiteManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multisite:manage {action} {--project=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage multisite database configuration and operations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'status':
                return $this->showStatus();
            case 'test':
                return $this->testConnections();
            case 'migrate':
                return $this->migrateToMultisite();
            case 'setup':
                return $this->setupMultisiteDatabase();
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: status, test, migrate, setup");
                return 1;
        }
    }

    private function showStatus(): int
    {
        $this->info('=== Multisite Configuration Status ===');
        
        $this->info("Multisite Mode: ALWAYS ENABLED (Fixed Configuration)");
        $this->info("Target Database: " . env('MULTISITE_DB_DATABASE', 'u712054581_Database_01'));
        $this->info("Database Host: " . env('MULTISITE_DB_HOST', '127.0.0.1'));
        $this->info("Database Username: " . env('MULTISITE_DB_USERNAME', 'u712054581_Database_01'));
        
        $this->warn("All projects will use the same database with project_id scoping");

        return 0;
    }

    private function testConnections(): int
    {
        $this->info('=== Testing Database Connections ===');

        // Test main database
        try {
            DB::connection('mysql')->getPdo();
            $this->info('✓ Main database connection: OK');
        } catch (\Exception $e) {
            $this->error('✗ Main database connection: FAILED');
            $this->error($e->getMessage());
            return 1;
        }

        // Test multisite database (always enabled now)
        try {
            Config::set('database.connections.multisite_test', [
                'driver' => 'mysql',
                'host' => env('MULTISITE_DB_HOST', '127.0.0.1'),
                'port' => env('MULTISITE_DB_PORT', '3306'),
                'database' => env('MULTISITE_DB_DATABASE', 'u712054581_Database_01'),
                'username' => env('MULTISITE_DB_USERNAME', 'u712054581_Database_01'),
                'password' => env('MULTISITE_DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            DB::connection('multisite_test')->getPdo();
            $this->info('✓ Multisite database connection: OK');
            $this->info('  Database: ' . env('MULTISITE_DB_DATABASE', 'u712054581_Database_01'));
            
            // Clean up test connection
            DB::purge('multisite_test');
            
        } catch (\Exception $e) {
            $this->error('✗ Multisite database connection: FAILED');
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }

    private function migrateToMultisite(): int
    {
        $this->info('=== Migrating to Multisite Mode ===');
        
        if (env('MULTISITE_ENABLED', false)) {
            $this->warn('Multisite is already enabled!');
            return 0;
        }

        $this->warn('This will help you migrate from separate databases to a single multisite database.');
        $this->warn('Make sure you have backed up your data before proceeding!');
        
        if (!$this->confirm('Do you want to continue?')) {
            $this->info('Migration cancelled.');
            return 0;
        }

        // Check if multisite database is configured
        if (!env('MULTISITE_DB_DATABASE')) {
            $this->error('Please configure MULTISITE_DB_* variables in .env first!');
            return 1;
        }

        // Test multisite database connection
        try {
            Config::set('database.connections.multisite_test', [
                'driver' => 'mysql',
                'host' => env('MULTISITE_DB_HOST', env('DB_HOST', '127.0.0.1')),
                'port' => env('MULTISITE_DB_PORT', env('DB_PORT', '3306')),
                'database' => env('MULTISITE_DB_DATABASE', 'multisite_db'),
                'username' => env('MULTISITE_DB_USERNAME', env('DB_USERNAME', 'root')),
                'password' => env('MULTISITE_DB_PASSWORD', env('DB_PASSWORD', '')),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            DB::connection('multisite_test')->getPdo();
            $this->info('✓ Multisite database connection: OK');
            
        } catch (\Exception $e) {
            $this->error('✗ Cannot connect to multisite database: ' . $e->getMessage());
            return 1;
        }

        // Get list of projects
        $projects = \App\Models\Project::where('status', 'active')->get();
        
        if ($projects->isEmpty()) {
            $this->info('No active projects found to migrate.');
            return 0;
        }

        $this->info("Found {$projects->count()} active projects to migrate:");
        foreach ($projects as $project) {
            $this->line("- {$project->code} ({$project->name})");
        }

        if (!$this->confirm('Proceed with data migration?')) {
            $this->info('Migration cancelled.');
            return 0;
        }

        // Perform migration
        $this->info('Starting migration...');
        
        foreach ($projects as $project) {
            $this->info("Migrating project: {$project->code}");
            
            try {
                $this->migrateProjectData($project);
                $this->info("✓ Successfully migrated: {$project->code}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to migrate {$project->code}: " . $e->getMessage());
            }
        }

        $this->info('Migration completed!');
        $this->info('Next steps:');
        $this->info('1. Set MULTISITE_ENABLED=true in .env');
        $this->info('2. Test your projects');
        $this->info('3. Remove old project databases if everything works');

        return 0;
    }

    private function migrateProjectData($project): void
    {
        // Get project database name
        $projectDbName = $this->getProjectDatabaseName($project);
        
        // Tables to migrate
        $tablesToMigrate = ['users', 'settings', 'menus', 'menu_items', 'widgets', 'widget_templates', 'posts', 'products', 'categories'];
        
        foreach ($tablesToMigrate as $table) {
            try {
                // Check if table exists in project database
                $exists = DB::select("SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ?", [$projectDbName, $table]);
                
                if (empty($exists)) {
                    continue;
                }
                
                // Get data from project database
                $data = DB::select("SELECT * FROM `{$projectDbName}`.`{$table}`");
                
                if (empty($data)) {
                    continue;
                }
                
                // Insert into multisite database with project_id
                DB::connection('multisite_test')->transaction(function() use ($table, $data, $project) {
                    foreach ($data as $row) {
                        $rowArray = (array) $row;
                        $rowArray['project_id'] = $project->id;
                        
                        // Remove id to let auto-increment handle it
                        unset($rowArray['id']);
                        
                        DB::connection('multisite_test')->table($table)->insert($rowArray);
                    }
                });
                
                $this->line("  ✓ Migrated {$table}: " . count($data) . " records");
                
            } catch (\Exception $e) {
                $this->line("  ✗ Failed to migrate {$table}: " . $e->getMessage());
            }
        }
    }

    private function getProjectDatabaseName($project): string
    {
        $code = $project->code;
        
        if (empty($code)) {
            $code = 'project_'.$project->id;
        }
        
        if (app()->environment('production')) {
            $username = env('DB_USERNAME', '');
            if (preg_match('/^(u\d+)_/', $username, $matches)) {
                $userPrefix = $matches[1];
                return $userPrefix . '_' . strtolower($code);
            }
        }
        
        return 'project_'.strtolower($code);
    }

    private function setupMultisiteDatabase(): int
    {
        $this->info('=== Setting up Multisite Database ===');
        
        if (!env('MULTISITE_ENABLED', false)) {
            $this->error('Multisite is not enabled in .env file!');
            $this->info('Please set MULTISITE_ENABLED=true and configure multisite database settings.');
            return 1;
        }

        // Test connection first
        if ($this->testConnections() !== 0) {
            $this->error('Cannot setup multisite database due to connection issues.');
            return 1;
        }

        $this->info('✓ Multisite database is configured and accessible.');
        $this->info('You can now run migrations on the multisite database if needed.');

        return 0;
    }
}
