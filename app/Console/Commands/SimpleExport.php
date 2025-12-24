<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use ZipArchive;

class SimpleExport extends Command
{
    protected $signature = 'project:simple-export {projectCode}';
    protected $description = 'Simple export test';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("Project not found!");
            return 1;
        }
        
        $exportDir = storage_path("app/simple-export/{$projectCode}");
        
        // Clean directory
        if (File::exists($exportDir)) {
            File::deleteDirectory($exportDir);
        }
        File::makeDirectory($exportDir, 0755, true);
        
        // Create structure
        File::makeDirectory("{$exportDir}/routes", 0755, true);
        File::makeDirectory("{$exportDir}/app/Http/Middleware", 0755, true);
        File::makeDirectory("{$exportDir}/bootstrap", 0755, true);
        
        // Create files
        $this->info("Creating routes/web.php...");
        $webRoutes = "<?php\n\n// Standalone CMS Routes\nRoute::get('/', function() { return 'Hello CMS'; });";
        File::put("{$exportDir}/routes/web.php", $webRoutes);
        
        $this->info("Creating middleware...");
        $middleware = "<?php\n\nnamespace App\\Http\\Middleware;\n\nclass StandaloneCMS {}";
        File::put("{$exportDir}/app/Http/Middleware/StandaloneCMS.php", $middleware);
        
        $this->info("Creating bootstrap/app.php...");
        $bootstrap = "<?php\n\n// Standalone bootstrap";
        File::put("{$exportDir}/bootstrap/app.php", $bootstrap);
        
        // Create zip
        $zipPath = storage_path("app/simple-export/{$projectCode}_simple.zip");
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($exportDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            $fileCount = 0;
            foreach ($iterator as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($exportDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                    $fileCount++;
                    $this->info("Added: {$relativePath}");
                }
            }
            
            $zip->close();
            File::deleteDirectory($exportDir);
            
            $this->info("✅ Simple export created with {$fileCount} files");
            $this->info("📦 Location: {$zipPath}");
        }
        
        return 0;
    }
}