<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $projectCode = $request->route('projectCode');
        
        if ($projectCode) {
            $project = Project::where('code', $projectCode)->first();
            
            if (!$project) {
                abort(404, 'Project not found');
            }
            
            $request->attributes->set('project', $project);
        }

        return $next($request);
    }
}
