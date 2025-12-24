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
        
        $multisiteEnabled = env('MULTISITE_ENABLED', false);
        $this->info("Multisite Enabled: " . ($multisiteEnabled ? 'YES' : 'NO'));
        
        if ($multisiteEnabled) {
            $this->info("Multisite DB Host: " . env('MULTISITE_DB_HOST', 'Not set'));
            $this->info("Multisite DB Database: " . env('MULTISITE_DB_DATABASE', 'Not set'));
            $this->info("Multisite DB Username: " . env('MULTISITE_DB_USERNAME', 'Not set'));
        } else {
            $this->warn("Using legacy mode (separate database per project)");
        }

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

        // Test multisite database if enabled
        if (env('MULTISITE_ENABLED', false)) {
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
                
                // Clean up test connection
                DB::purge('multisite_test');
                
            } catch (\Exception $e) {
                $this->error('✗ Multisite database connection: FAILED');
                $this->error($e->getMessage());
                return 1;
            }
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

        // Instructions for manual migration
        $this->info('To complete the migration:');
        $this->info('1. Create a new database for multisite');
        $this->info('2. Update your .env file with multisite configuration:');
        $this->info('   MULTISITE_ENABLED=true');
        $this->info('   MULTISITE_DB_HOST=your_host');
        $this->info('   MULTISITE_DB_DATABASE=your_multisite_db');
        $this->info('   MULTISITE_DB_USERNAME=your_username');
        $this->info('   MULTISITE_DB_PASSWORD=your_password');
        $this->info('3. Run migrations on the multisite database');
        $this->info('4. Import existing project data with project_id scoping');

        return 0;
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
