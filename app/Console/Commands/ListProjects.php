<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

class ListProjects extends Command
{
    protected $signature = 'project:list';
    protected $description = 'List all available projects';

    public function handle()
    {
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->info("No projects found.");
            return;
        }
        
        $this->info("Available Projects:");
        $this->info("==================");
        
        foreach ($projects as $project) {
            $this->info("Code: {$project->code}");
            $this->info("Name: {$project->name}");
            $this->info("Domain: {$project->domain}");
            $this->info("Status: " . ($project->is_active ? 'Active' : 'Inactive'));
            $this->info("Database: project_" . strtolower($project->code));
            $this->info("---");
        }
        
        $this->info("\nTo export a project:");
        $this->info("php artisan project:export {project_code}");
    }
}