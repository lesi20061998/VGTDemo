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

        // Multi-site: Each project has its own database
        $projectDbName = 'project_'.strtolower($code);

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

        DB::purge('project');

        // Set default connection to project for this request
        DB::setDefaultConnection('project');
        Config::set('database.default', 'project');
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
