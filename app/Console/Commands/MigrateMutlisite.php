<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class MigrateMutlisite extends Command
{
    protected $signature = 'multisite:migrate';
    protected $description = 'Run migrations for all multisite projects';

    public function handle()
    {
        $projects = Project::all();
        
        $this->info("Found {$projects->count()} projects");
        
        foreach ($projects as $project) {
            $this->info("Migrating project: {$project->code}");
            
            try {
                // Switch to project database
                Config::set('database.connections.project', [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => $project->code . '_db',
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ]);
                
                // Test connection
                DB::connection('project')->getPdo();
                
                // Run specific migrations
                $this->call('migrate', [
                    '--database' => 'project',
                    '--path' => 'database/migrations/2024_01_15_000001_add_project_id_to_products_enhanced.php',
                    '--force' => true
                ]);
                
                $this->call('migrate', [
                    '--database' => 'project', 
                    '--path' => 'database/migrations/2024_01_15_000002_add_project_id_to_multisite_tables.php',
                    '--force' => true
                ]);
                
                // Update existing records with project_id
                $this->updateProjectIds($project);
                
                $this->info("✅ Migrated: {$project->code}");
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to migrate {$project->code}: " . $e->getMessage());
            }
        }
        
        $this->info("Migration completed!");
    }
    
    private function updateProjectIds($project)
    {
        $tables = [
            'products_enhanced',
            'product_categories', 
            'brands',
            'orders',
            'menus',
            'widgets',
            'settings',
            'posts',
            'pages'
        ];
        
        foreach ($tables as $table) {
            try {
                DB::connection('project')
                    ->table($table)
                    ->whereNull('project_id')
                    ->update(['project_id' => $project->id]);
                    
                $count = DB::connection('project')->table($table)->count();
                $this->info("  Updated {$count} records in {$table}");
                
            } catch (\Exception $e) {
                $this->warn("  Skipped {$table}: " . $e->getMessage());
            }
        }
    }
}