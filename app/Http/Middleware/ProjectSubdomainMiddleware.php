<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;

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
            
            if (!$project) {
                $project = $this->createProjectAutomatically($projectCode);
                
                if (!$project) {
                    abort(404, 'Project not found: ' . $projectCode);
                }
            }
            
            // Ensure project has CMS user
            $this->ensureProjectHasCmsUser($project);
            
            view()->share('currentProject', $project);
            $request->attributes->set('project', $project);
        }
        
        return $next($request);
    }
    
    private function createProjectAutomatically($projectCode)
    {
        try {
            return DB::transaction(function () use ($projectCode) {
                // Tạo hoặc lấy employee mặc định
                $employee = Employee::firstOrCreate(
                    ['code' => 'AUTO_ADMIN'],
                    [
                        'name' => 'Auto Admin',
                        'email' => 'auto.admin@system.local',
                        'position' => 'System Admin',
                        'department' => 'admin',
                        'is_active' => true
                    ]
                );
                
                // Tạo contract mặc định
                $contract = Contract::firstOrCreate(
                    ['contract_code' => 'AUTO_' . $projectCode],
                    [
                        'employee_id' => $employee->id,
                        'full_code' => 'AUTO_' . $projectCode . '_' . date('Y'),
                        'client_name' => 'Auto Generated Client',
                        'service_type' => 'Web Development',
                        'requirements' => 'Auto generated project requirements',
                        'start_date' => now(),
                        'end_date' => now()->addMonths(12),
                        'deadline' => now()->addMonths(12),
                        'status' => 'approved',
                        'is_active' => true
                    ]
                );
                
                // Tạo user account cho project
                $user = \App\Models\User::firstOrCreate(
                    ['username' => $projectCode],
                    [
                        'name' => 'Admin - ' . $projectCode,
                        'username' => $projectCode,
                        'email' => strtolower($projectCode) . '@project.local',
                        'password' => bcrypt('admin123'),
                        'role' => 'cms',
                        'level' => 2,
                        'email_verified_at' => now(),
                    ]
                );
                
                // Tạo project
                $project = Project::create([
                    'contract_id' => $contract->id,
                    'name' => 'Auto Project ' . $projectCode,
                    'code' => $projectCode,
                    'client_name' => 'Auto Generated Client',
                    'start_date' => now(),
                    'deadline' => now()->addMonths(12),
                    'status' => 'active',
                    'contract_value' => 50000000,
                    'technical_requirements' => 'Laravel CMS/E-commerce System',
                    'features' => 'Content Management, Product Management, Order Management',
                    'environment' => 'Production',
                    'project_admin_username' => $projectCode,
                    'project_admin_password' => 'admin123',
                    'admin_id' => $employee->id,
                    'created_by' => $employee->id,
                    'approved_at' => now(),
                    'initialized_at' => now()
                ]);
                
                \Log::info('Auto created project: ' . $projectCode);
                
                return $project;
            });
        } catch (\Exception $e) {
            \Log::error('Failed to auto create project: ' . $e->getMessage());
            return null;
        }
    }
    
    private function ensureProjectHasCmsUser($project)
    {
        // Check if project already has a CMS user
        $existingUser = \App\Models\User::where('username', $project->code)
            ->where('role', 'cms')
            ->first();
            
        if (!$existingUser) {
            try {
                // Create CMS user for existing project
                $user = \App\Models\User::create([
                    'name' => 'Admin - ' . $project->code,
                    'username' => $project->code,
                    'email' => strtolower($project->code) . '@project.local',
                    'password' => bcrypt($project->project_admin_password ?? 'admin123'),
                    'role' => 'cms',
                    'level' => 2,
                    'email_verified_at' => now(),
                ]);
                
                \Log::info('Created CMS user for existing project: ' . $project->code);
                
                return $user;
            } catch (\Exception $e) {
                \Log::error('Failed to create CMS user for project ' . $project->code . ': ' . $e->getMessage());
            }
        }
        
        return $existingUser;
    }
}

