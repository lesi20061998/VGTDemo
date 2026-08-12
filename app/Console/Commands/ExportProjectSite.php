<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportProjectSite extends Command
{
  protected $signature = 'project:export {projectCode} {--output-path=exports/} {--cms-only : Export only CMS functionality}';

  protected $description = 'Export a project as standalone Laravel CMS site';

  public function handle()
  {
    $projectCode = $this->argument('projectCode');
    $outputPath = $this->option('output-path');
    $cmsOnly = $this->option('cms-only');

    $project = Project::where('code', $projectCode)->first();

    if (! $project) {
      $this->error("Project with code '{$projectCode}' not found!");

      return 1;
    }

    $this->info(" Exporting project CMS: {$project->name} ({$projectCode})");

    if ($cmsOnly) {
      $this->info(' Exporting CMS-only functionality (no SuperAdmin)');
    }

    // Tạo thư mục export
    $exportDir = storage_path("app/{$outputPath}/{$projectCode}");
    $this->createExportDirectory($exportDir);

    // 1. Copy Laravel core files
    $this->copyLaravelCore($exportDir);

    // 2. Export database
    $this->exportProjectDatabase($project, $exportDir);

    // 3. Generate project-specific config
    $this->generateProjectConfig($project, $exportDir);

    // 4. Copy project assets
    $this->copyProjectAssets($project, $exportDir);

    // 5. Generate CMS routes and controllers
    $this->generateCMSRoutes($project, $exportDir);

    // 6. Copy CMS controllers and middleware
    $this->copyCMSControllers($exportDir);

    // 7. Generate deployment files
    $this->generateDeploymentFiles($project, $exportDir);

    // 8. Create zip file
    $zipPath = $this->createZipFile($projectCode, $exportDir);

    $this->info(' Project CMS exported successfully!');
    $this->info(" Export location: {$zipPath}");
    $this->info(' Ready to deploy as standalone CMS site');

    return 0;
  }

  private function createExportDirectory($exportDir)
  {
    if (File::exists($exportDir)) {
      File::deleteDirectory($exportDir);
    }
    File::makeDirectory($exportDir, 0755, true);
    $this->info(' Created export directory');
  }

  private function copyLaravelCore($exportDir)
  {
    $this->info(' Copying essential Laravel files...');

    // Chỉ copy những thư mục cần thiết, bỏ qua vendor để giảm thời gian
    $essentialDirs = [
      'app',
      'bootstrap',
      'config',
      'database',
      'public',
      'resources',
    ];

    $coreFiles = [
      'artisan',
      'composer.json',
      '.htaccess',
    ];

    // Copy directories (bỏ qua vendor và storage để tăng tốc)
    foreach ($essentialDirs as $dir) {
      if (File::exists(base_path($dir))) {
        $this->info(" Copying {$dir}...");
        File::copyDirectory(base_path($dir), "{$exportDir}/{$dir}");
      }
    }

    // Tạo storage structure thay vì copy
    $this->createStorageStructure($exportDir);

    // Copy files
    foreach ($coreFiles as $file) {
      if (File::exists(base_path($file))) {
        File::copy(base_path($file), "{$exportDir}/{$file}");
      }
    }

    $this->info(' Essential Laravel files copied');
    $this->warn("️ Note: vendor/ not copied - run 'composer install' on target server");
  }

  private function createStorageStructure($exportDir)
  {
    $storageDirs = [
      'storage/app/public',
      'storage/framework/cache',
      'storage/framework/sessions',
      'storage/framework/views',
      'storage/logs',
    ];

    foreach ($storageDirs as $dir) {
      File::ensureDirectoryExists("{$exportDir}/{$dir}");
    }

    // Tạo .gitignore cho storage
    File::put("{$exportDir}/storage/logs/.gitignore", "*\n!.gitignore\n");
  }

  private function exportProjectDatabase($project, $exportDir)
  {
    $this->info(' Exporting project database...');

    $dbName = 'project_'.strtolower($project->code);
    $sqlFile = "{$exportDir}/database.sql";

    try {
      // Setup project database connection
      config(['database.connections.export_project' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST'),
        'port' => env('DB_PORT'),
        'database' => $dbName,
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
      ]]);

      // Export database structure and data
      $this->exportDatabaseToSQL($dbName, $sqlFile);

      $this->info(' Database exported to database.sql');

    } catch (\Exception $e) {
      $this->error(' Database export failed: '.$e->getMessage());
    }
  }

  private function exportDatabaseToSQL($dbName, $sqlFile)
  {
    // Export qua PHP nếu không có mysqldump
    $this->exportDatabaseViaPHP($dbName, $sqlFile);
  }

  private function exportDatabaseViaPHP($dbName, $sqlFile)
  {
    try {
      $tables = DB::connection('export_project')->select('SHOW TABLES');
      $sql = "-- Database Export for {$dbName}\n";
      $sql .= '-- Generated on '.date('Y-m-d H:i:s')."\n\n";

      foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];

        // Get table structure
        $createTable = DB::connection('export_project')->select("SHOW CREATE TABLE `{$tableName}`");
        $sql .= "-- Table: {$tableName}\n";
        $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        $sql .= $createTable[0]->{'Create Table'}.";\n\n";

        // Get table data
        $rows = DB::connection('export_project')->table($tableName)->get();

        if ($rows->count() > 0) {
          $sql .= "-- Data for table {$tableName}\n";
          $sql .= "INSERT INTO `{$tableName}` VALUES\n";

          $values = [];
          foreach ($rows as $row) {
            $rowData = array_map(function ($value) {
              return is_null($value) ? 'NULL' : "'".addslashes($value)."'";
            }, (array) $row);
            $values[] = '('.implode(',', $rowData).')';
          }

          $sql .= implode(",\n", $values).";\n\n";
        }
      }

      File::put($sqlFile, $sql);
    } catch (\Exception $e) {
      $this->warn('Database export via PHP failed: '.$e->getMessage());
      // Tạo file SQL trống
      File::put($sqlFile, "-- Database export failed\n-- Please export manually\n");
    }
  }

  private function generateProjectConfig($project, $exportDir)
  {
    $this->info('️ Generating project-specific config...');

    // Tạo .env file cho project
    $envContent = $this->generateProjectEnv($project);
    File::put("{$exportDir}/.env", $envContent);

    $this->info(' Project config generated');
  }

  private function generateProjectEnv($project)
  {
    return "APP_NAME=\"{$project->name}\"
APP_ENV=production
APP_KEY=".config('app.key')."
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

# Project specific settings
PROJECT_CODE={$project->code}
PROJECT_ID={$project->id}
PROJECT_NAME=\"{$project->name}\"
";
  }

  private function generateCMSRoutes($project, $exportDir)
  {
    // Tạo routes/web.php cho CMS project
    $routesContent = "<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Project CMS: {$project->name} ({$project->code})

Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Auth::routes();

// CMS Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/', function () {
    return redirect()->route('admin.dashboard');
  });
  
  Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
  
  // Products
  Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
  Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
  Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
  
  // Orders
  Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
  
  // CMS Content
  Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
  Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
  Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
  Route::resource('widgets', \App\Http\Controllers\Admin\WidgetController::class);
  
  // Settings
  Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
  Route::post('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
  
  // Users & Roles (project level only)
  Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
  Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
});

// Frontend routes
Route::get('/products', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [\App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [\App\Http\Controllers\Frontend\CategoryController::class, 'show'])->name('categories.show');

// API routes for frontend
Route::prefix('api')->group(function () {
  Route::get('products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
  Route::get('categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
  Route::get('menus', [\App\Http\Controllers\Api\MenuController::class, 'index']);
  Route::get('widgets/{area}', [\App\Http\Controllers\Api\WidgetController::class, 'getByArea']);
});
";

    File::put("{$exportDir}/routes/web.php", $routesContent);

    // Tạo routes/api.php đơn giản
    $apiRoutesContent = "<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request \$request) {
  return \$request->user();
})->middleware('auth:sanctum');

// CMS API routes
Route::prefix('cms')->group(function () {
  Route::get('products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
  Route::get('categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
  Route::get('posts', [\App\Http\Controllers\Api\PostController::class, 'index']);
  Route::get('menus', [\App\Http\Controllers\Api\MenuController::class, 'index']);
  Route::get('widgets/{area}', [\App\Http\Controllers\Api\WidgetController::class, 'getByArea']);
});
";

    File::put("{$exportDir}/routes/api.php", $apiRoutesContent);
  }

  private function copyCMSControllers($exportDir)
  {
    $this->info(' Copying CMS controllers...');

    // Controllers cần thiết cho CMS
    $cmsControllers = [
      'Admin/DashboardController.php',
      'Admin/ProductController.php',
      'Admin/CategoryController.php',
      'Admin/BrandController.php',
      'Admin/OrderController.php',
      'Admin/PostController.php',
      'Admin/PageController.php',
      'Admin/MenuController.php',
      'Admin/WidgetController.php',
      'Admin/SettingsController.php',
      'Admin/UserController.php',
      'Admin/RoleController.php',
      'Frontend/ProductController.php',
      'Frontend/CategoryController.php',
      'Api/ProductController.php',
      'Api/CategoryController.php',
      'Api/PostController.php',
      'Api/MenuController.php',
      'Api/WidgetController.php',
      'HomeController.php',
    ];

    foreach ($cmsControllers as $controller) {
      $sourcePath = app_path("Http/Controllers/{$controller}");
      $destPath = "{$exportDir}/app/Http/Controllers/{$controller}";

      if (File::exists($sourcePath)) {
        // Tạo thư mục nếu chưa có
        File::ensureDirectoryExists(dirname($destPath));
        File::copy($sourcePath, $destPath);
      }
    }

    $this->info(' CMS controllers copied');
  }

  private function copyProjectAssets($project, $exportDir)
  {
    $this->info(' Copying project assets...');

    // Copy project-specific uploads
    $uploadsPath = storage_path("app/public/projects/{$project->code}");
    if (File::exists($uploadsPath)) {
      File::copyDirectory($uploadsPath, "{$exportDir}/storage/app/public/uploads");
    }

    // Copy compiled assets
    if (File::exists(public_path('build'))) {
      File::copyDirectory(public_path('build'), "{$exportDir}/public/build");
    }

    $this->info(' Project assets copied');
  }

  private function generateDeploymentFiles($project, $exportDir)
  {
    $this->info(' Generating deployment files...');

    // Tạo README.md cho CMS
    $readmeContent = "# {$project->name} - CMS

## CMS Deployment Instructions

### 1. Upload files
Upload all files to your hosting provider

### 2. Database Setup
1. Create database: `{$project->code}_cms`
2. Import database: `mysql -u username -p {$project->code}_cms < database.sql`

### 3. Configuration
1. Update `.env` file with your database credentials
2. Set proper file permissions:
  ```bash
  chmod -R 755 storage/
  chmod -R 755 bootstrap/cache/
  ```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## CMS Features Included
- Product Management
- Category Management 
- Brand Management
- Order Management
- Content Management (Posts, Pages)
- Menu Management
- Widget Management
- Settings Management
- User & Role Management (project level)
- Frontend Display
- API Endpoints

## CMS Access
- Admin Panel: `/admin`
- Login: Use seeded admin account or register new user
- Frontend: `/`

## Project Details
- Code: {$project->code}
- Name: {$project->name}
- Domain: {$project->domain}
- Type: Standalone CMS
- Exported: ".date('Y-m-d H:i:s').'

## Note
This is a standalone CMS export. SuperAdmin functionality is not included.
Each project operates independently with its own database and users.
';

    File::put("{$exportDir}/README.md", $readmeContent);

    // Tạo deployment script
    $deployScript = "#!/bin/bash
# CMS Deployment script for {$project->name}

echo \" Deploying {$project->name} CMS...\"

# Set permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate key if needed
if grep -q \"APP_KEY=$\" .env; then
  php artisan key:generate
fi

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo \" CMS Deployment completed!\"
echo \" Access admin panel at: /admin\"
";

    File::put("{$exportDir}/deploy.sh", $deployScript);
    chmod("{$exportDir}/deploy.sh", 0755);

    $this->info(' Deployment files generated');
  }

  private function createZipFile($projectCode, $exportDir)
  {
    $this->info(' Creating zip file...');

    $zipPath = storage_path("app/exports/{$projectCode}_cms.zip");

    // Tạo thư mục exports nếu chưa có
    File::ensureDirectoryExists(dirname($zipPath));

    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

      $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($exportDir),
        \RecursiveIteratorIterator::LEAVES_ONLY
      );

      foreach ($iterator as $file) {
        if (! $file->isDir()) {
          $filePath = $file->getRealPath();
          $relativePath = substr($filePath, strlen($exportDir) + 1);
          $zip->addFile($filePath, $relativePath);
        }
      }

      $zip->close();

      // Cleanup export directory
      File::deleteDirectory($exportDir);

      return $zipPath;
    }

    throw new \Exception('Cannot create zip file');
  }
}
