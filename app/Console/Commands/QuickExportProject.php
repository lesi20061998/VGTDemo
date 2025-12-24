<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use ZipArchive;

class QuickExportProject extends Command
{
    protected $signature = 'project:quick-export {projectCode}';
    protected $description = 'Quick export project configuration and database info';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("Project with code '{$projectCode}' not found!");
            return 1;
        }
        
        $this->info("🚀 Quick export for: {$project->name} ({$projectCode})");
        
        // Tạo thư mục export
        $exportDir = storage_path("app/quick-exports/{$projectCode}");
        
        if (File::exists($exportDir)) {
            File::deleteDirectory($exportDir);
        }
        File::makeDirectory($exportDir, 0755, true);
        
        // Tạo các file cấu hình
        $this->createConfigFiles($project, $exportDir);
        $this->createRouteFiles($project, $exportDir);
        $this->createDeploymentGuide($project, $exportDir);
        
        // Tạo zip file
        $zipPath = $this->createZip($projectCode, $exportDir);
        
        $this->info("✅ Quick export completed!");
        $this->info("📦 Export: {$zipPath}");
        
        return 0;
    }
    
    private function createConfigFiles($project, $exportDir)
    {
        $this->info("⚙️  Creating config files...");
        
        // .env file
        $envContent = "APP_NAME=\"{$project->name}\"
APP_ENV=production
APP_KEY=" . config('app.key') . "
APP_DEBUG=false
APP_URL=https://{$project->domain}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE={$project->code}_cms
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Project Settings
PROJECT_CODE={$project->code}
PROJECT_ID={$project->id}
PROJECT_NAME=\"{$project->name}\"
";
        File::put("{$exportDir}/.env", $envContent);
        
        // Database info
        $dbInfo = "# Database Information

## Current Database (Multi-tenant)
- Database: project_" . strtolower($project->code) . "
- Host: " . env('DB_HOST') . "
- Username: " . env('DB_USERNAME') . "

## Standalone Database (After Export)
- Database: {$project->code}_cms
- Host: localhost (or your hosting DB host)
- Username: your_db_username
- Password: your_db_password

## Export Steps
1. Export current database: project_" . strtolower($project->code) . "
2. Create new database: {$project->code}_cms
3. Import data to new database
4. Update .env file with new credentials
";
        File::put("{$exportDir}/database-info.md", $dbInfo);
    }
    
    private function createRouteFiles($project, $exportDir)
    {
        $this->info("🛣️  Creating route files...");
        
        File::ensureDirectoryExists("{$exportDir}/routes");
        
        // web.php
        $webRoutes = "<?php

use Illuminate\Support\Facades\Route;

// {$project->name} CMS Routes

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Auth::routes();

// Admin CMS Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Product Management
    Route::resource('products', App\\Http\\Controllers\\Admin\\ProductController::class);
    Route::resource('categories', App\\Http\\Controllers\\Admin\\CategoryController::class);
    Route::resource('brands', App\\Http\\Controllers\\Admin\\BrandController::class);
    
    // Content Management
    Route::resource('posts', App\\Http\\Controllers\\Admin\\PostController::class);
    Route::resource('pages', App\\Http\\Controllers\\Admin\\PageController::class);
    Route::resource('menus', App\\Http\\Controllers\\Admin\\MenuController::class);
    Route::resource('widgets', App\\Http\\Controllers\\Admin\\WidgetController::class);
    
    // Settings
    Route::get('settings', [App\\Http\\Controllers\\Admin\\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\\Http\\Controllers\\Admin\\SettingsController::class, 'update'])->name('settings.update');
    
    // Users & Roles
    Route::resource('users', App\\Http\\Controllers\\Admin\\UserController::class);
    Route::resource('roles', App\\Http\\Controllers\\Admin\\RoleController::class);
});

// Frontend Routes
Route::get('/products', [App\\Http\\Controllers\\Frontend\\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [App\\Http\\Controllers\\Frontend\\ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [App\\Http\\Controllers\\Frontend\\CategoryController::class, 'show'])->name('categories.show');
";
        File::put("{$exportDir}/routes/web.php", $webRoutes);
        
        // api.php
        $apiRoutes = "<?php

use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Route;

Route::get('/user', function (Request \$request) {
    return \$request->user();
})->middleware('auth:sanctum');

// CMS API
Route::prefix('cms')->group(function () {
    Route::get('products', [App\\Http\\Controllers\\Api\\ProductController::class, 'index']);
    Route::get('categories', [App\\Http\\Controllers\\Api\\CategoryController::class, 'index']);
    Route::get('menus', [App\\Http\\Controllers\\Api\\MenuController::class, 'index']);
    Route::get('widgets/{area}', [App\\Http\\Controllers\\Api\\WidgetController::class, 'getByArea']);
});
";
        File::put("{$exportDir}/routes/api.php", $apiRoutes);
    }
    
    private function createDeploymentGuide($project, $exportDir)
    {
        $this->info("📋 Creating deployment guide...");
        
        $guide = "# {$project->name} - CMS Deployment Guide

## Overview
This is a standalone CMS export for project: **{$project->name}** (Code: {$project->code})

## What's Included
- ✅ Environment configuration (.env)
- ✅ Route definitions (web.php, api.php)
- ✅ Database information and migration guide
- ✅ Deployment instructions

## What You Need to Add
- Laravel framework files (app/, vendor/, etc.)
- Controllers and Models from original system
- Views and assets
- Database export/import

## Deployment Steps

### 1. Prepare Laravel Application
```bash
# Create new Laravel project or copy from original
composer create-project laravel/laravel {$project->code}-cms
cd {$project->code}-cms

# Or copy from original system:
# - app/ directory
# - resources/ directory  
# - config/ directory
# - database/ directory
```

### 2. Database Setup
```bash
# Export from original system
mysqldump -u username -p project_" . strtolower($project->code) . " > {$project->code}_export.sql

# Create new database
mysql -u username -p -e \"CREATE DATABASE {$project->code}_cms\"

# Import data
mysql -u username -p {$project->code}_cms < {$project->code}_export.sql
```

### 3. Configuration
```bash
# Copy .env file from this export
cp .env /path/to/laravel-app/.env

# Update database credentials in .env
# Update APP_URL in .env

# Generate new app key
php artisan key:generate
```

### 4. Routes Setup
```bash
# Copy route files
cp routes/web.php /path/to/laravel-app/routes/web.php
cp routes/api.php /path/to/laravel-app/routes/api.php
```

### 5. Final Steps
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## CMS Features
- Product Management
- Category & Brand Management
- Content Management (Posts, Pages)
- Menu & Widget Management
- User & Role Management
- Settings Management
- Frontend Display
- API Endpoints

## Access Points
- **Admin Panel**: `/admin`
- **Frontend**: `/`
- **API**: `/api/cms/`

## Support
- Project Code: {$project->code}
- Project ID: {$project->id}
- Export Date: " . date('Y-m-d H:i:s') . "
- Original Database: project_" . strtolower($project->code) . "
- New Database: {$project->code}_cms

## Notes
- This export does NOT include SuperAdmin functionality
- Each CMS operates independently
- Remember to update .env with correct database credentials
- Test all functionality after deployment
";
        
        File::put("{$exportDir}/README.md", $guide);
        
        // Deployment checklist
        $checklist = "# Deployment Checklist for {$project->name}

## Pre-deployment
- [ ] Export database from project_" . strtolower($project->code) . "
- [ ] Create new database: {$project->code}_cms
- [ ] Prepare Laravel application files
- [ ] Update hosting DNS/domain settings

## Deployment
- [ ] Upload Laravel files to hosting
- [ ] Copy .env file and update credentials
- [ ] Copy routes files
- [ ] Import database
- [ ] Run composer install
- [ ] Generate app key
- [ ] Set file permissions
- [ ] Cache configuration

## Post-deployment
- [ ] Test admin login at /admin
- [ ] Test frontend at /
- [ ] Test API endpoints at /api/cms/
- [ ] Verify database connections
- [ ] Check error logs
- [ ] Test all CMS features

## Troubleshooting
- Check storage/logs/laravel.log for errors
- Verify .env database credentials
- Ensure proper file permissions
- Check PHP version compatibility
- Verify all required PHP extensions
";
        
        File::put("{$exportDir}/deployment-checklist.md", $checklist);
    }
    
    private function createZip($projectCode, $exportDir)
    {
        $zipPath = storage_path("app/quick-exports/{$projectCode}_config.zip");
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($exportDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($iterator as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($exportDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            
            $zip->close();
            return $zipPath;
        }
        
        throw new \Exception("Cannot create zip file");
    }
}