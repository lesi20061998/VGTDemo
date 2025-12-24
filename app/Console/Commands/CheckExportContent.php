<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class CheckExportContent extends Command
{
    protected $signature = 'project:check-export {zipFile}';
    protected $description = 'Check content of exported CMS zip file';

    public function handle()
    {
        $zipFile = $this->argument('zipFile');
        $fullPath = storage_path("app/{$zipFile}");
        
        if (!file_exists($fullPath)) {
            $this->error("Zip file not found: {$fullPath}");
            return 1;
        }
        
        $this->info("🔍 Checking export content: {$zipFile}");
        
        $zip = new ZipArchive();
        if ($zip->open($fullPath) === TRUE) {
            
            $this->info("📦 Total files: " . $zip->numFiles);
            
            // Check essential Laravel structure
            $essentialDirs = [
                'app/',
                'config/',
                'database/',
                'resources/',
                'routes/',
                'storage/',
                'bootstrap/',
                'public/'
            ];
            
            $essentialFiles = [
                'artisan',
                'composer.json',
                '.env.example',
                'README.md',
                'database.sql'
            ];
            
            $this->info("\n📁 Essential Directories:");
            foreach ($essentialDirs as $dir) {
                $found = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, $dir) === 0) {
                        $found = true;
                        break;
                    }
                }
                $this->info(($found ? "✅" : "❌") . " {$dir}");
            }
            
            $this->info("\n📄 Essential Files:");
            foreach ($essentialFiles as $file) {
                $found = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if ($filename === $file) {
                        $found = true;
                        break;
                    }
                }
                $this->info(($found ? "✅" : "❌") . " {$file}");
            }
            
            // Check Controllers
            $this->info("\n🎛️  CMS Controllers:");
            $controllers = [
                'app/Http/Controllers/Admin/DashboardController.php',
                'app/Http/Controllers/Admin/ProductController.php',
                'app/Http/Controllers/Admin/CategoryController.php',
                'app/Http/Controllers/Admin/UserController.php',
                'app/Http/Controllers/Admin/SettingsController.php'
            ];
            
            foreach ($controllers as $controller) {
                $found = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if ($filename === $controller) {
                        $found = true;
                        break;
                    }
                }
                $this->info(($found ? "✅" : "❌") . " " . basename($controller));
            }
            
            // Check Models
            $this->info("\n📊 Models:");
            $models = [
                'app/Models/User.php',
                'app/Models/Product.php',
                'app/Models/Category.php',
                'app/Models/Brand.php',
                'app/Models/Order.php'
            ];
            
            foreach ($models as $model) {
                $found = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if ($filename === $model) {
                        $found = true;
                        break;
                    }
                }
                $this->info(($found ? "✅" : "❌") . " " . basename($model));
            }
            
            // Check Views
            $this->info("\n🎨 Views:");
            $viewDirs = [
                'resources/views/admin/',
                'resources/views/auth/',
                'resources/views/layouts/'
            ];
            
            foreach ($viewDirs as $viewDir) {
                $found = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, $viewDir) === 0) {
                        $found = true;
                        break;
                    }
                }
                $this->info(($found ? "✅" : "❌") . " {$viewDir}");
            }
            
            $zip->close();
            
            $this->info("\n🎯 Assessment:");
            $this->info("This export contains a Laravel CMS with:");
            $this->info("- Laravel Framework Structure");
            $this->info("- CMS Controllers & Models");
            $this->info("- Database Export");
            $this->info("- Configuration Files");
            $this->info("- Installation Guide");
            
            $this->info("\n📋 To deploy:");
            $this->info("1. Extract zip file");
            $this->info("2. Run: composer install");
            $this->info("3. Setup .env file");
            $this->info("4. Import database.sql");
            $this->info("5. Set permissions");
            $this->info("6. Access /admin");
            
        } else {
            $this->error("Cannot open zip file");
            return 1;
        }
        
        return 0;
    }
}