<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class ListZipContent extends Command
{
    protected $signature = 'project:list-zip {zipFile}';
    protected $description = 'List content of zip file';

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
            
            $this->info("📦 Files in zip ({$zip->numFiles} total):");
            
            // List first 50 files
            for ($i = 0; $i < min(50, $zip->numFiles); $i++) {
                $filename = $zip->getNameIndex($i);
                $this->info("  {$filename}");
            }
            
            if ($zip->numFiles > 50) {
                $this->info("  ... and " . ($zip->numFiles - 50) . " more files");
            }
            
            $zip->close();
            
        } else {
            $this->error("Cannot open zip file");
            return 1;
        }
        
        return 0;
    }
}