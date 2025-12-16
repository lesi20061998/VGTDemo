<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;

class ProjectAdminSeeder extends Seeder
{
    public function run()
    {
        $projects = Project::all();
        
        foreach ($projects as $project) {
            $password = Project::generateProjectAdminPassword();
            $username = $project->code;
            $email = strtolower($project->code) . '@project.local';
            
            DB::table('users')->updateOrInsert(
                ['username' => $username],
                [
                    'name' => 'CMS Admin - ' . $project->code,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'cms',
                    'level' => 2,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            $project->update([
                'project_admin_username' => $username,
                'project_admin_password' => $password
            ]);
        }
    }
}