<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class QuickCheckZip extends Command
{
    protected $signature = 'zip:check {zipFile}';
    protected $description = 'Quick check zip file contents';

    public function handle()
    {
        $zipFile = $this->argument('zipFile');
        $fullPath = storage_path("app/{$zipFile}");
        
        if (!file_exists($fullPath)) {
            $this->error("Zip file not found: {$fullPath}");
            return 1;
        }
        
        $zip = new ZipArchive();
        if ($zip->open($fullPath) === TRUE) {
            
            $this->info("📦 Total files: " . $zip->numFiles);
            
            // Check specific files
            $checkFiles = [
                'routes/web.php',
                'routes/api.php', 
                'routes/console.php',
                'app/Http/Middleware/StandaloneCMS.php',
                'bootstrap/app.php',
                'README.md',
                'database.sql',
                '.env.example'
            ];
            
            $this->info("\n🔍 Checking key files:");
            foreach ($checkFiles as $file) {
                $found = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $zipFileName = $zip->getNameIndex($i);
                    // Normalize path separators
                    $normalizedZipFile = str_replace('\\', '/', $zipFileName);
                    $normalizedCheckFile = str_replace('\\', '/', $file);
                    
                    if ($normalizedZipFile === $normalizedCheckFile) {
                        $found = true;
                        break;
                    }
                }
                $this->info(($found ? "✅" : "❌") . " {$file}");
            }
            
            $zip->close();
            
        } else {
            $this->error("Cannot open zip file");
            return 1;
        }
        
        return 0;
    }
}