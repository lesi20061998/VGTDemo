<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SetProjectDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->attributes->get('project');
        
        if ($project) {
            // Set database connection for this project
            $this->setProjectDatabase($project->code);
        }
        
        return $next($request);
    }
    
    private function setProjectDatabase($projectCode)
    {
        $projectDbName = 'project_' . strtolower($projectCode);
        
        Config::set('database.connections.project', [
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
        ]);
        
        DB::purge('project');
        DB::setDefaultConnection('project');
        
        Config::set('database.default', 'project');
    }
    

}