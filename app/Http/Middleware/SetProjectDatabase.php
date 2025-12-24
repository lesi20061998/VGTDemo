<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetProjectDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->attributes->get('project');

        if ($project) {
            // Set database connection for this project
            $this->setProjectDatabase($project, $request);
        }

        $response = $next($request);

        // Reset to main database after request
        if ($project) {
            $this->resetToMainDatabase();
        }

        return $response;
    }

    private function setProjectDatabase($project, Request $request)
    {
        $code = $project->code;

        // Fallback to project ID if code is empty
        if (empty($code)) {
            $code = 'project_'.$project->id;
        }

        Log::debug("SetProjectDatabase: Setting up project context for {$code}");

        // Store main database name for later reset
        $request->attributes->set('main_database', config('database.default'));

        // Set tenant ID cho SettingsService
        session(['current_tenant_id' => $project->id]);

        // Clear settings cache để load lại từ project database
        if (class_exists('\App\Services\SettingsService')) {
            \App\Services\SettingsService::getInstance()->clearCache();
        }

        // Always use multisite database (no more legacy mode)
        $this->setupMultisiteDatabase($project, $code);
    }

    private function setupMultisiteDatabase($project, $code)
    {
        Log::info("Using multisite database for project: {$code}");

        // Use fixed multisite database configuration
        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('MULTISITE_DB_HOST', '127.0.0.1'),
            'port' => env('MULTISITE_DB_PORT', '3306'),
            'database' => env('MULTISITE_DB_DATABASE', 'u712054581_Database_01'),
            'username' => env('MULTISITE_DB_USERNAME', 'u712054581_Database_01'),
            'password' => env('MULTISITE_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        try {
            DB::purge('project');
            
            // Test connection before switching
            DB::connection('project')->getPdo();
            
            // Set default connection to project for this request
            DB::setDefaultConnection('project');
            Config::set('database.default', 'project');
            
            // Set project context for multisite queries
            app()->instance('current_project_id', $project->id);
            session(['current_project_id' => $project->id]);
            
            Log::info("Successfully connected to multisite database for project: {$code}");
            
        } catch (\Exception $e) {
            Log::error("Failed to connect to multisite database for project {$code}: " . $e->getMessage());
            $this->fallbackToMainDatabase($project);
        }
    }

    private function setupProjectDatabase($project, $code)
    {
        // Multi-site: Each project has its own database
        $projectDbName = 'project_'.strtolower($code);

        // HOSTINGER FIX: Use full database name with user prefix
        if (app()->environment('production')) {
            // On Hostinger, database names need user prefix
            $userPrefix = explode('_', env('DB_USERNAME'))[0] ?? 'u712054581';
            $projectDbName = $userPrefix . '_' . strtolower($code);
        }

        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $projectDbName, // Separate database for each project
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        try {
            DB::purge('project');
            
            // Test connection before switching
            DB::connection('project')->getPdo();
            
            // Set default connection to project for this request
            DB::setDefaultConnection('project');
            Config::set('database.default', 'project');
            
            Log::info("Successfully connected to project database: {$projectDbName}");
            
        } catch (\Exception $e) {
            Log::error("Failed to connect to project database {$projectDbName}: " . $e->getMessage());
            $this->fallbackToMainDatabase($project);
        }
    }

    private function fallbackToMainDatabase($project)
    {
        Log::warning("Falling back to main database with project scoping for project: {$project->code}");
        
        // Use main database but set project context
        Config::set('database.connections.project', config('database.connections.mysql'));
        DB::purge('project');
        
        // Set project ID for scoping queries
        app()->instance('current_project_id', $project->id);
        session(['fallback_project_id' => $project->id]);
    }

    private function resetToMainDatabase()
    {
        // Reset to mysql (main) connection
        DB::setDefaultConnection('mysql');
        Config::set('database.default', 'mysql');
        DB::purge('project');

        Log::debug('SetProjectDatabase: Reset to main database');
    }
}