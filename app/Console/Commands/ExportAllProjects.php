<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

class ExportAllProjects extends Command
{
    protected $signature = 'project:export-all {--active-only : Export only active projects}';
    protected $description = 'Export all projects as config packages';

    public function handle()
    {
        $activeOnly = $this->option('active-only');
        
        $query = Project::query();
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        $projects = $query->get();
        
        if ($projects->isEmpty()) {
            $this->info("No projects found to export.");
            return;
        }
        
        $this->info("🚀 Exporting {$projects->count()} projects...");
        
        $exported = [];
        $failed = [];
        
        foreach ($projects as $project) {
            $this->info("📦 Exporting: {$project->code} - {$project->name}");
            
            try {
                $this->call('project:quick-export', ['projectCode' => $project->code]);
                $exported[] = $project->code;
                $this->info("✅ {$project->code} exported successfully");
            } catch (\Exception $e) {
                $failed[] = $project->code;
                $this->error("❌ Failed to export {$project->code}: " . $e->getMessage());
            }
        }
        
        $this->info("\n🎉 Batch export completed!");
        $this->info("✅ Exported: " . count($exported) . " projects");
        
        if (!empty($failed)) {
            $this->error("❌ Failed: " . count($failed) . " projects");
        }
        
        $this->info("\n📁 All exports located in: storage/app/quick-exports/");
        
        return 0;
    }
}