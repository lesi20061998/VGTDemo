<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileMonitorController extends Controller
{
    public function index(Request $request)
    {
        $projectCode = $request->get('project', 'main');
        $logs = $this->getFileChangeLogs($projectCode);
        $projects = $this->getAvailableProjects();
        
        // If AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'logs' => $logs->values(),
                'project_code' => $projectCode,
                'total' => $logs->count()
            ]);
        }
        
        return view('superadmin.file-monitor.index', compact('logs', 'projects', 'projectCode'));
    }
    
    private function getFileChangeLogs($projectCode = 'main')
    {
        $logPath = storage_path('logs/file-changes-' . $projectCode . '.log');
        
        if (!File::exists($logPath)) {
            return collect();
        }
        
        $content = File::get($logPath);
        $lines = array_filter(explode("\n", $content));
        
        return collect($lines)->map(function($line) {
            $data = json_decode($line, true);
            return $data ? (object) $data : null;
        })->filter()->sortByDesc('timestamp')->take(100);
    }
    
    private function getAvailableProjects()
    {
        $projects = \DB::table('projects')->select('id', 'code', 'name')->get();
        
        // Add main system
        $projects->prepend((object) [
            'id' => null,
            'code' => 'main',
            'name' => 'Main System'
        ]);
        
        return $projects;
    }
    
    public function getRecentChanges()
    {
        $changes = [];
        $basePath = base_path();
        
        // Scan critical directories
        $directories = [
            'app/Http/Controllers',
            'app/Models', 
            'routes',
            'config',
            'database/migrations'
        ];
        
        foreach ($directories as $dir) {
            $fullPath = $basePath . '/' . $dir;
            if (is_dir($fullPath)) {
                $files = File::allFiles($fullPath);
                
                foreach ($files as $file) {
                    $relativePath = str_replace($basePath . '/', '', $file->getPathname());
                    $lastModified = filemtime($file->getPathname());
                    
                    // Only show files modified in last 24 hours
                    if ($lastModified > (time() - 86400)) {
                        $changes[] = [
                            'file' => $relativePath,
                            'modified' => date('Y-m-d H:i:s', $lastModified),
                            'size' => $file->getSize()
                        ];
                    }
                }
            }
        }
        
        return response()->json($changes);
    }
}