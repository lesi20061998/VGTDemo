<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MigrateProjectDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:migrate {projectCode : The project code to migrate} {--rollback : Rollback the last migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations on a specific project database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        $rollback = $this->option('rollback');

        $this->info("Migrating database for project: {$projectCode}");

        // Set up project database connection
        $databaseName = 'project_'.strtolower($projectCode);

        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $databaseName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        // Test connection
        try {
            DB::purge('project');
            DB::setDefaultConnection('project');
            $currentDb = DB::select('SELECT DATABASE() as db')[0]->db;
            $this->info("✅ Connected to database: {$currentDb}");
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: '.$e->getMessage());

            return 1;
        }

        // Run migration
        try {
            if ($rollback) {
                $this->info('Rolling back last migration...');
                Artisan::call('migrate:rollback', [
                    '--database' => 'project',
                    '--step' => 1,
                ]);
            } else {
                $this->info('Running migrations...');
                Artisan::call('migrate', [
                    '--database' => 'project',
                    '--path' => 'database/migrations/2025_12_19_032517_add_language_id_to_project_products_table.php',
                ]);
            }

            $this->info('✅ Migration completed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ Migration failed: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
