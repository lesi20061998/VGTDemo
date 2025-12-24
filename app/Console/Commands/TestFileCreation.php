<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestFileCreation extends Command
{
    protected $signature = 'test:files';
    protected $description = 'Test file creation';

    public function handle()
    {
        $testDir = storage_path('app/test-export');
        
        // Create directory
        if (File::exists($testDir)) {
            File::deleteDirectory($testDir);
        }
        File::makeDirectory($testDir, 0755, true);
        File::makeDirectory("{$testDir}/routes", 0755, true);
        File::makeDirectory("{$testDir}/app/Http/Middleware", 0755, true);
        File::makeDirectory("{$testDir}/bootstrap", 0755, true);
        
        // Test creating files
        $webRoutes = "<?php\n\n// Test routes\nRoute::get('/', function() { return 'Hello'; });";
        File::put("{$testDir}/routes/web.php", $webRoutes);
        
        $middleware = "<?php\n\nnamespace App\\Http\\Middleware;\n\nclass TestMiddleware {}";
        File::put("{$testDir}/app/Http/Middleware/TestMiddleware.php", $middleware);
        
        $bootstrap = "<?php\n\n// Test bootstrap";
        File::put("{$testDir}/bootstrap/app.php", $bootstrap);
        
        // Check if files exist
        $files = [
            'routes/web.php',
            'app/Http/Middleware/TestMiddleware.php',
            'bootstrap/app.php'
        ];
        
        foreach ($files as $file) {
            $fullPath = "{$testDir}/{$file}";
            if (File::exists($fullPath)) {
                $this->info("✅ Created: {$file}");
                $this->info("   Content: " . substr(File::get($fullPath), 0, 50) . "...");
            } else {
                $this->error("❌ Failed: {$file}");
            }
        }
        
        // List all files in directory
        $this->info("\n📁 All files in test directory:");
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($testDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $relativePath = substr($file->getRealPath(), strlen($testDir) + 1);
                $this->info("  {$relativePath}");
            }
        }
        
        return 0;
    }
}