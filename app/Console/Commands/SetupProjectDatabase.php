<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupProjectDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:setup {projectCode} {--seed : Run seeders after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup database for a specific project (migrate and seed)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        $shouldSeed = $this->option('seed');

        $this->info("🚀 Setting up database for project: {$projectCode}");

        // Set up project database connection
        $projectDbName = 'project_'.strtolower($projectCode);

        config(['database.connections.project' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $projectDbName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]]);

        // Clear any existing connection
        \DB::purge('project');

        try {
            // Test connection
            \DB::connection('project')->getPdo();
            $this->info("✅ Connected to database: {$projectDbName}");
        } catch (\Exception $e) {
            $this->error("❌ Cannot connect to database: {$projectDbName}");
            $this->error('Error: '.$e->getMessage());

            return 1;
        }

        // Run migrations
        $this->info('📦 Running migrations...');
        $this->call('migrate', [
            '--database' => 'project',
            '--force' => true,
        ]);

        // Run seeders if requested
        if ($shouldSeed) {
            $this->info('🌱 Running seeders...');

            // Set environment variable for seeders
            putenv("CURRENT_PROJECT_CODE={$projectCode}");

            $this->call('db:seed', [
                '--database' => 'project',
                '--class' => 'ProjectDatabaseSeeder',
                '--force' => true,
            ]);
        }

        $this->info("✅ Project {$projectCode} database setup completed!");
        $this->info("🎯 You can now access: /{$projectCode}/admin/products");

        return 0;
    }
}
