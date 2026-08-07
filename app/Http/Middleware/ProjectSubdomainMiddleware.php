<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectSubdomainMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $projectCode = $request->route('projectCode');

        // Block placeholder URLs
        if ($projectCode && (str_contains($projectCode, '{') || str_contains($projectCode, '}'))) {
            abort(404, 'Invalid project code format');
        }

        if ($projectCode) {
            $project = Project::where('code', $projectCode)->first();

            if (! $project) {
                abort(404, 'Project not found: '.$projectCode);
            }

            view()->share('currentProject', $project);
            $request->attributes->set('project', $project);
        }

        return $next($request);
    }

    private function ensureProjectHasCmsUser($project)
    {
        // Check if project already has a CMS user
        $existingUser = User::where('username', $project->code)
            ->where('role', 'cms')
            ->first();

        if (! $existingUser) {
            try {
                // Create CMS user for existing project
                $user = User::create([
                    'name' => 'Admin - '.$project->code,
                    'username' => $project->code,
                    'email' => strtolower($project->code).'@project.local',
                    'password' => bcrypt($project->project_admin_password ?? 'admin123'),
                    'role' => 'cms',
                    'level' => 2,
                    'email_verified_at' => now(),
                ]);

                \Log::info('Created CMS user for existing project: '.$project->code);

                return $user;
            } catch (\Exception $e) {
                \Log::error('Failed to create CMS user for project '.$project->code.': '.$e->getMessage());
            }
        }

        return $existingUser;
    }
}
