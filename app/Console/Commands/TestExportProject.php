<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\File;

class TestExportProject extends Command
{
    protected $signature = 'project:test-export {projectCode}';
    protected $description = 'Test export project (lightweight version)';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("Project with code '{$projectCode}' not found!");
            return 1;
        }
        
        $this->info("🚀 Testing export for: {$project->name} ({$projectCode})");
        
        // Tạo thư mục export
        $exportDir = storage_path("app/test-exports/{$projectCode}");
        
        if (File::exists($exportDir)) {
            File::deleteDirectory($exportDir);
        }
        File::makeDirectory($exportDir, 0755, true);
        
        // Tạo các file cơ bản
        $this->createBasicFiles($project, $exportDir);
        
        $this->info("✅ Test export completed!");
        $this->info("📁 Location: {$exportDir}");
        
        return 0;
    }
    
    private function createBasicFiles($project, $exportDir)
    {
        // Tạo .env
        $envContent = "APP_NAME=\"{$project->name}\"
APP_ENV=production
APP_KEY=" . config('app.key') . "
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE={$project->code}_cms
DB_USERNAME=your_username
DB_PASSWORD=your_password

PROJECT_CODE={$project->code}
PROJECT_NAME=\"{$project->name}\"
";
        File::put("{$exportDir}/.env", $envContent);
        
        // Tạo README.md
        $readmeContent = "# {$project->name} - CMS Export

## Project Details
- Code: {$project->code}
- Name: {$project->name}
- Database: {$project->code}_cms
- Exported: " . date('Y-m-d H:i:s') . "

## Next Steps
1. Create database: {$project->code}_cms
2. Upload files to hosting
3. Update .env with database credentials
4. Run: php artisan migrate --seed
";
        File::put("{$exportDir}/README.md", $readmeContent);
        
        // Tạo database info
        $dbInfo = "Database Name: project_" . strtolower($project->code) . "
Export Database Name: {$project->code}_cms
Project ID: {$project->id}
";
        File::put("{$exportDir}/database-info.txt", $dbInfo);
        
        $this->info("✅ Basic files created");
    }
}