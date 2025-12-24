<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use ZipArchive;

class ExportCompleteCMS extends Command
{
    protected $signature = 'project:export-complete {projectCode}';
    protected $description = 'Export complete CMS project with Laravel source and database';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("Project with code '{$projectCode}' not found!");
            return 1;
        }
        
        $this->info("🚀 Exporting COMPLETE CMS: {$project->name} ({$projectCode})");
        $this->info("📦 Includes: Laravel Framework + CMS + Database + Config");
        
        // Tạo thư mục export
        $exportDir = storage_path("app/cms-exports/{$projectCode}");
        $this->createExportDirectory($exportDir);
        
        // 1. Copy Laravel framework
        $this->copyLaravelFramework($exportDir);
        
        // 2. Export database
        $this->exportProjectDatabase($project, $exportDir);
        
        // 3. Generate CMS config
        $this->generateCMSConfig($project, $exportDir);
        
        // 4. Create installer
        $this->createInstaller($project, $exportDir);
        
        // 5. Create zip
        $zipPath = $this->createZip($projectCode, $exportDir);
        
        $this->info("✅ COMPLETE CMS exported successfully!");
        $this->info("📦 Location: {$zipPath}");
        
        return 0;
    }
    
    private function createExportDirectory($exportDir)
    {
        if (File::exists($exportDir)) {
            File::deleteDirectory($exportDir);
        }
        File::makeDirectory($exportDir, 0755, true);
        $this->info("📁 Created export directory");
    }
    
    private function copyLaravelFramework($exportDir)
    {
        $this->info("📋 Copying Laravel framework...");
        
        $dirs = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage'];
        $files = ['artisan', 'composer.json', 'composer.lock', '.htaccess'];
        
        foreach ($dirs as $dir) {
            if (File::exists(base_path($dir))) {
                $this->info("  📂 Copying {$dir}...");
                File::copyDirectory(base_path($dir), "{$exportDir}/{$dir}");
            }
        }
        
        foreach ($files as $file) {
            if (File::exists(base_path($file))) {
                File::copy(base_path($file), "{$exportDir}/{$file}");
            }
        }
        
        $this->info("✅ Laravel framework copied");
    }
    
    private function exportProjectDatabase($project, $exportDir)
    {
        $this->info("💾 Exporting database...");
        
        $dbName = 'project_' . strtolower($project->code);
        $sqlFile = "{$exportDir}/database.sql";
        
        try {
            Config::set('database.connections.export_project', [
                'driver' => 'mysql',
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => $dbName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ]);
            
            DB::purge('export_project');
            DB::connection('export_project')->getPdo();
            
            $this->exportDatabase($dbName, $sqlFile);
            $this->info("✅ Database exported");
            
        } catch (\Exception $e) {
            $this->error("❌ Database export failed: " . $e->getMessage());
            $this->createDatabaseInstructions($project, $sqlFile);
        }
    }
    
    private function exportDatabase($dbName, $sqlFile)
    {
        $sql = "-- CMS Database Export: {$dbName}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        try {
            $tables = DB::connection('export_project')->select('SHOW TABLES');
            
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                // Table structure
                $createTable = DB::connection('export_project')->select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                // Table data
                $rows = DB::connection('export_project')->table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data for {$tableName}\n";
                    foreach ($rows->chunk(50) as $chunk) {
                        $values = [];
                        foreach ($chunk as $row) {
                            $rowData = array_map(function($value) {
                                return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                            }, (array)$row);
                            $values[] = '(' . implode(',', $rowData) . ')';
                        }
                        $sql .= "INSERT INTO `{$tableName}` VALUES " . implode(',', $values) . ";\n";
                    }
                    $sql .= "\n";
                }
            }
            
            File::put($sqlFile, $sql);
            
        } catch (\Exception $e) {
            $this->createDatabaseInstructions($project, $sqlFile);
        }
    }
    
    private function createDatabaseInstructions($project, $sqlFile)
    {
        $instructions = "-- Manual Database Export Required
-- Original: project_" . strtolower($project->code) . "
-- Target: {$project->code}_cms
-- Export: mysqldump -u user -p project_" . strtolower($project->code) . " > database.sql
-- Import: mysql -u user -p {$project->code}_cms < database.sql";
        
        File::put($sqlFile, $instructions);
    }
    
    private function generateCMSConfig($project, $exportDir)
    {
        $this->info("⚙️  Generating CMS config...");
        
        // .env
        $envContent = "APP_NAME=\"{$project->name} CMS\"
APP_ENV=production
APP_KEY=" . config('app.key') . "
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE={$project->code}_cms
DB_USERNAME=your_username
DB_PASSWORD=your_password

PROJECT_CODE={$project->code}
PROJECT_NAME=\"{$project->name}\"
";
        
        File::put("{$exportDir}/.env.example", $envContent);
        
        // Routes - CMS only
        $routes = "<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\\Http\\Controllers\\Admin\\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('products', App\\Http\\Controllers\\Admin\\ProductController::class);
    Route::resource('categories', App\\Http\\Controllers\\Admin\\CategoryController::class);
    Route::resource('brands', App\\Http\\Controllers\\Admin\\BrandController::class);
    Route::resource('orders', App\\Http\\Controllers\\Admin\\OrderController::class);
    Route::resource('posts', App\\Http\\Controllers\\Admin\\PostController::class);
    Route::resource('users', App\\Http\\Controllers\\Admin\\UserController::class);
    
    Route::get('settings', [App\\Http\\Controllers\\Admin\\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\\Http\\Controllers\\Admin\\SettingsController::class, 'update'])->name('settings.update');
});

Route::get('/products', [App\\Http\\Controllers\\Frontend\\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [App\\Http\\Controllers\\Frontend\\ProductController::class, 'show'])->name('products.show');
";
        
        File::put("{$exportDir}/routes/web.php", $routes);
        
        $this->info("✅ CMS config generated");
    }
    
    private function createInstaller($project, $exportDir)
    {
        $this->info("🔧 Creating installer...");
        
        $readme = "# {$project->name} - Complete CMS

## Quick Installation

1. **Extract files**
   ```bash
   unzip {$project->code}_cms_complete.zip
   cd {$project->code}_cms_complete/
   ```

2. **Install dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # Create database
   mysql -u username -p -e \"CREATE DATABASE {$project->code}_cms\"
   
   # Import data
   mysql -u username -p {$project->code}_cms < database.sql
   ```

5. **Configure .env**
   Update database credentials in .env file

6. **Set permissions**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

7. **Access CMS**
   - Admin: `/admin`
   - Frontend: `/`

## Features
- Product Management
- Category & Brand Management
- Order Management
- Content Management
- User Management
- Settings

## Requirements
- PHP 8.1+
- MySQL 5.7+
- Composer

Project: {$project->name} ({$project->code})
Export: " . date('Y-m-d H:i:s') . "
";
        
        File::put("{$exportDir}/README.md", $readme);
        
        $this->info("✅ Installer created");
    }
    
    private function createZip($projectCode, $exportDir)
    {
        $this->info("📦 Creating zip...");
        
        $zipPath = storage_path("app/cms-exports/{$projectCode}_cms_complete.zip");
        File::ensureDirectoryExists(dirname($zipPath));
        
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
                    
                    if ($fileCount % 100 == 0) {
                        $this->info("    📦 Added {$fileCount} files...");
                    }
                }
            }
            
            $zip->close();
            File::deleteDirectory($exportDir);
            
            $this->info("✅ Zip created with {$fileCount} files");
            return $zipPath;
        }
        
        throw new \Exception("Cannot create zip file");
    }
}