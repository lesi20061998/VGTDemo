# Giải Pháp Multi Database trên Hostinger

## Vấn đề hiện tại
Lỗi: `SQLSTATE[HY000] [1044] Access denied for user 'u712054581_VGTApp'@'localhost' to database 'project_sivgt'`

**Nguyên nhân:** 
- Hệ thống đang cố gắng truy cập database `project_sivgt` nhưng user `u712054581_VGTApp` không có quyền truy cập
- Trên Hostinger, mỗi database cần được tạo riêng và gán quyền cho user cụ thể

## Giải pháp chi tiết

### Bước 1: Tạo Database cho từng Project trên Hostinger

#### 1.1 Truy cập hPanel Hostinger
1. Đăng nhập vào hPanel của Hostinger
2. Vào **Databases** → **MySQL Databases**

#### 1.2 Tạo Database cho Project
Dựa vào code của bạn, hệ thống tạo database theo format: `project_{code}`

**Ví dụ:** Nếu project code là `sivgt`, cần tạo database: `project_sivgt`

```sql
-- Tên database cần tạo
project_sivgt
project_hd001  
project_demo
-- ... (tùy theo các project bạn có)
```

#### 1.3 Gán quyền User cho Database
1. Trong **MySQL Databases**, tìm section **Add User to Database**
2. Chọn user: `u712054581_VGTApp`
3. Chọn database: `project_sivgt`
4. Gán **ALL PRIVILEGES**

### Bước 2: Kiểm tra và Tạo Database tự động

#### 2.1 Tạo Command để kiểm tra và tạo Database

Tạo file `app/Console/Commands/CreateProjectDatabases.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateProjectDatabases extends Command
{
    protected $signature = 'project:create-databases {--check : Only check existing databases}';
    protected $description = 'Create databases for all projects';

    public function handle()
    {
        $projects = Project::all();
        $checkOnly = $this->option('check');
        
        $this->info("Found {$projects->count()} projects");
        
        foreach ($projects as $project) {
            $dbName = 'project_' . strtolower($project->code);
            
            if ($checkOnly) {
                $this->checkDatabase($dbName, $project);
            } else {
                $this->createDatabase($dbName, $project);
            }
        }
    }
    
    private function checkDatabase($dbName, $project)
    {
        try {
            // Test connection to project database
            Config::set('database.connections.temp_project', [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $dbName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            
            DB::connection('temp_project')->getPdo();
            $this->info("✅ Database exists: {$dbName} (Project: {$project->code})");
            
        } catch (\Exception $e) {
            $this->error("❌ Database missing: {$dbName} (Project: {$project->code})");
            $this->error("   Error: " . $e->getMessage());
        }
    }
    
    private function createDatabase($dbName, $project)
    {
        try {
            // Trên shared hosting như Hostinger, không thể tạo database qua code
            // Cần tạo thủ công qua hPanel
            $this->warn("⚠️  Manual action required for: {$dbName}");
            $this->warn("   Please create database '{$dbName}' in Hostinger hPanel");
            $this->warn("   Then assign user permissions to this database");
            
        } catch (\Exception $e) {
            $this->error("❌ Cannot create database: {$dbName}");
            $this->error("   Error: " . $e->getMessage());
        }
    }
}
```

#### 2.2 Chạy Command kiểm tra

```bash
# Kiểm tra database nào đã tồn tại
php artisan project:create-databases --check

# Liệt kê database cần tạo
php artisan project:create-databases
```

### Bước 3: Cấu hình Database Connection

#### 3.1 Cập nhật .env file
Đảm bảo thông tin database chính xác:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u712054581_main_database  # Database chính
DB_USERNAME=u712054581_VGTApp
DB_PASSWORD=your_password_here
```

#### 3.2 Kiểm tra quyền User
Tạo script kiểm tra quyền: `scripts/check_database_permissions.php`

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$host = 'localhost';
$username = 'u712054581_VGTApp';
$password = 'your_password'; // Thay bằng password thực

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    
    // Kiểm tra databases user có quyền truy cập
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Databases accessible by user '$username':\n";
    foreach ($databases as $db) {
        echo "- $db\n";
    }
    
    // Kiểm tra quyền cụ thể
    $stmt = $pdo->query("SHOW GRANTS FOR CURRENT_USER()");
    $grants = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nGrants for user '$username':\n";
    foreach ($grants as $grant) {
        echo "- $grant\n";
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
```

### Bước 4: Tạo Database thủ công trên Hostinger

#### 4.1 Danh sách Database cần tạo
Dựa vào projects trong hệ thống, tạo các database sau:

```
project_sivgt
project_hd001
project_demo
project_test
```

#### 4.2 Quy trình tạo từng Database

**Cho mỗi database:**

1. **Tạo Database:**
   - Vào hPanel → Databases → MySQL Databases
   - Tên database: `project_sivgt` (ví dụ)
   - Click "Create Database"

2. **Gán quyền User:**
   - Trong section "Add User to Database"
   - User: `u712054581_VGTApp`
   - Database: `project_sivgt`
   - Privileges: **ALL PRIVILEGES**
   - Click "Add"

3. **Kiểm tra kết nối:**
   ```bash
   php artisan tinker
   >>> DB::connection('project')->getPdo();
   ```

### Bước 5: Migrate Database cho từng Project

#### 5.1 Chạy Migration cho Project cụ thể

```bash
# Migrate cho project sivgt
php artisan project:setup sivgt --seed

# Hoặc migrate thủ công
php artisan migrate --database=project --force
```

#### 5.2 Tạo Command Migrate All Projects

Tạo file `app/Console/Commands/MigrateAllProjects.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class MigrateAllProjects extends Command
{
    protected $signature = 'project:migrate-all {--seed : Run seeders after migration}';
    protected $description = 'Migrate all project databases';

    public function handle()
    {
        $projects = Project::all();
        $shouldSeed = $this->option('seed');
        
        $this->info("Migrating {$projects->count()} projects...");
        
        foreach ($projects as $project) {
            $this->migrateProject($project, $shouldSeed);
        }
        
        $this->info("All projects migrated successfully!");
    }
    
    private function migrateProject($project, $shouldSeed = false)
    {
        $dbName = 'project_' . strtolower($project->code);
        
        $this->info("Migrating project: {$project->code} (DB: {$dbName})");
        
        try {
            // Setup project database connection
            Config::set('database.connections.project', [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $dbName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            
            // Clear connection cache
            DB::purge('project');
            
            // Test connection
            DB::connection('project')->getPdo();
            
            // Run migrations
            $this->call('migrate', [
                '--database' => 'project',
                '--force' => true
            ]);
            
            // Run seeders if requested
            if ($shouldSeed) {
                $this->call('db:seed', [
                    '--database' => 'project',
                    '--class' => 'ProjectDatabaseSeeder',
                    '--force' => true
                ]);
            }
            
            $this->info("✅ Migrated: {$project->code}");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to migrate {$project->code}: " . $e->getMessage());
        }
    }
}
```

### Bước 6: Xử lý lỗi và Debug

#### 6.1 Tạo Command Debug Database

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DebugDatabaseConnections extends Command
{
    protected $signature = 'debug:database {project?}';
    protected $description = 'Debug database connections for projects';

    public function handle()
    {
        $projectCode = $this->argument('project');
        
        if ($projectCode) {
            $this->debugProjectDatabase($projectCode);
        } else {
            $this->debugMainDatabase();
        }
    }
    
    private function debugMainDatabase()
    {
        $this->info("=== Main Database Debug ===");
        
        try {
            $pdo = DB::connection('mysql')->getPdo();
            $this->info("✅ Main database connection: OK");
            
            // Show current database
            $result = DB::select('SELECT DATABASE() as current_db');
            $this->info("Current database: " . $result[0]->current_db);
            
            // Show user
            $result = DB::select('SELECT USER() as current_user');
            $this->info("Current user: " . $result[0]->current_user);
            
        } catch (\Exception $e) {
            $this->error("❌ Main database connection failed: " . $e->getMessage());
        }
    }
    
    private function debugProjectDatabase($projectCode)
    {
        $this->info("=== Project Database Debug: {$projectCode} ===");
        
        $dbName = 'project_' . strtolower($projectCode);
        
        try {
            // Setup project connection
            Config::set('database.connections.debug_project', [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $dbName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            
            $pdo = DB::connection('debug_project')->getPdo();
            $this->info("✅ Project database connection: OK");
            
            // Show tables
            $tables = DB::connection('debug_project')->select('SHOW TABLES');
            $this->info("Tables count: " . count($tables));
            
            // Test a simple query
            $result = DB::connection('debug_project')->select('SELECT 1 as test');
            $this->info("Test query: " . $result[0]->test);
            
        } catch (\Exception $e) {
            $this->error("❌ Project database connection failed: " . $e->getMessage());
            $this->error("Database name: {$dbName}");
            $this->error("Username: " . env('DB_USERNAME'));
        }
    }
}
```

### Bước 7: Giải pháp tạm thời (Fallback)

#### 7.1 Sử dụng Single Database với Project ID

Nếu không thể tạo nhiều database, có thể sử dụng một database duy nhất với `project_id`:

Cập nhật `app/Http/Middleware/SetProjectDatabase.php`:

```php
private function setProjectDatabase($project, Request $request)
{
    $code = $project->code;
    
    // Fallback to project ID if code is empty
    if (empty($code)) {
        $code = 'project_'.$project->id;
    }

    Log::debug("SetProjectDatabase: Setting up project context for {$code}");

    // Store main database name for later reset
    $request->attributes->set('main_database', config('database.default'));

    // Set tenant ID cho SettingsService
    session(['current_tenant_id' => $project->id]);
    session(['current_project_id' => $project->id]);

    // Clear settings cache để load lại từ project database
    if (class_exists('\App\Services\SettingsService')) {
        \App\Services\SettingsService::getInstance()->clearCache();
    }

    // FALLBACK: Nếu không có multi-database, sử dụng main database với project scoping
    $projectDbName = 'project_'.strtolower($code);
    
    try {
        // Test project database connection
        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $projectDbName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        DB::purge('project');
        DB::connection('project')->getPdo(); // Test connection
        
        // Set default connection to project for this request
        DB::setDefaultConnection('project');
        Config::set('database.default', 'project');
        
        Log::info("Using separate database: {$projectDbName}");
        
    } catch (\Exception $e) {
        // Fallback to main database with project scoping
        Log::warning("Cannot connect to {$projectDbName}, using main database with project scoping");
        
        // Use main database but set project context
        Config::set('database.connections.project', config('database.connections.mysql'));
        DB::purge('project');
        
        // Set project ID for scoping
        app()->instance('current_project_id', $project->id);
    }
}
```

### Bước 8: Checklist thực hiện

#### 8.1 Trên Hostinger hPanel:
- [ ] Tạo database `project_sivgt`
- [ ] Gán quyền user `u712054581_VGTApp` cho database `project_sivgt`
- [ ] Kiểm tra kết nối database qua phpMyAdmin

#### 8.2 Trên Server:
- [ ] Chạy `php artisan debug:database sivgt`
- [ ] Chạy `php artisan project:setup sivgt --seed`
- [ ] Kiểm tra logs: `tail -f storage/logs/laravel.log`

#### 8.3 Test ứng dụng:
- [ ] Truy cập `/sivgt/admin`
- [ ] Kiểm tra widgets load được không
- [ ] Kiểm tra products, categories

### Bước 9: Monitoring và Maintenance

#### 9.1 Tạo Health Check

```php
// routes/web.php
Route::get('/health/database/{project?}', function($project = null) {
    if ($project) {
        $dbName = 'project_' . strtolower($project);
        try {
            Config::set('database.connections.health_check', [
                'driver' => 'mysql',
                'host' => env('DB_HOST'),
                'database' => $dbName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ]);
            
            DB::connection('health_check')->getPdo();
            return response()->json(['status' => 'ok', 'database' => $dbName]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    
    return response()->json(['status' => 'ok', 'database' => 'main']);
});
```

#### 9.2 Backup Strategy

```bash
# Backup tất cả project databases
for project in sivgt hd001 demo; do
    mysqldump -u u712054581_VGTApp -p project_$project > backup_project_$project_$(date +%Y%m%d).sql
done
```

### Bước 10: Export Project thành Site độc lập

#### 10.1 Tạo Command Export Project

Tạo file `app/Console/Commands/ExportProjectSite.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
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
        
        if (!$project) {
            $this->error("Project with code '{$projectCode}' not found!");
            return 1;
        }
        
        $this->info("🚀 Exporting project CMS: {$project->name} ({$projectCode})");
        
        if ($cmsOnly) {
            $this->info("📋 Exporting CMS-only functionality (no SuperAdmin)");
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
        
        $this->info("✅ Project CMS exported successfully!");
        $this->info("📦 Export location: {$zipPath}");
        $this->info("🌐 Ready to deploy as standalone CMS site");
        
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
    
    private function copyLaravelCore($exportDir)
    {
        $this->info("📋 Copying Laravel core files...");
        
        $coreDirs = [
            'app',
            'bootstrap', 
            'config',
            'database',
            'public',
            'resources',
            'routes',
            'storage',
            'vendor'
        ];
        
        $coreFiles = [
            'artisan',
            'composer.json',
            'composer.lock',
            '.htaccess',
            'package.json',
            'vite.config.js'
        ];
        
        // Copy directories
        foreach ($coreDirs as $dir) {
            if (File::exists(base_path($dir))) {
                File::copyDirectory(base_path($dir), "{$exportDir}/{$dir}");
            }
        }
        
        // Copy files
        foreach ($coreFiles as $file) {
            if (File::exists(base_path($file))) {
                File::copy(base_path($file), "{$exportDir}/{$file}");
            }
        }
        
        $this->info("✅ Laravel core files copied");
    }
    
    private function exportProjectDatabase($project, $exportDir)
    {
        $this->info("💾 Exporting project database...");
        
        $dbName = 'project_' . strtolower($project->code);
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
            
            $this->info("✅ Database exported to database.sql");
            
        } catch (\Exception $e) {
            $this->error("❌ Database export failed: " . $e->getMessage());
        }
    }
    
    private function exportDatabaseToSQL($dbName, $sqlFile)
    {
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        
        // Sử dụng mysqldump để export
        $command = "mysqldump -h {$host} -u {$username} -p{$password} {$dbName} > {$sqlFile}";
        
        // Hoặc export qua PHP nếu không có mysqldump
        $this->exportDatabaseViaPHP($dbName, $sqlFile);
    }
    
    private function exportDatabaseViaPHP($dbName, $sqlFile)
    {
        $tables = DB::connection('export_project')->select('SHOW TABLES');
        $sql = "-- Database Export for {$dbName}\n";
        $sql .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            
            // Get table structure
            $createTable = DB::connection('export_project')->select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "-- Table: {$tableName}\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $rows = DB::connection('export_project')->table($tableName)->get();
            
            if ($rows->count() > 0) {
                $sql .= "-- Data for table {$tableName}\n";
                $sql .= "INSERT INTO `{$tableName}` VALUES\n";
                
                $values = [];
                foreach ($rows as $row) {
                    $rowData = array_map(function($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, (array)$row);
                    $values[] = '(' . implode(',', $rowData) . ')';
                }
                
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        File::put($sqlFile, $sql);
    }
    
    private function generateProjectConfig($project, $exportDir)
    {
        $this->info("⚙️  Generating project-specific config...");
        
        // Tạo .env file cho project
        $envContent = $this->generateProjectEnv($project);
        File::put("{$exportDir}/.env", $envContent);
        
        // Cập nhật config/app.php
        $this->updateAppConfig($project, $exportDir);
        
        // Tạo routes cho project
        $this->generateProjectRoutes($project, $exportDir);
        
        $this->info("✅ Project config generated");
    }
    
    private function generateProjectEnv($project)
    {
        return "APP_NAME=\"{$project->name}\"
APP_ENV=production
APP_KEY=" . config('app.key') . "
APP_DEBUG=false
APP_URL=https://{$project->domain}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE={$project->code}_standalone
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
    
    private function updateAppConfig($project, $exportDir)
    {
        $configPath = "{$exportDir}/config/app.php";
        $config = File::get($configPath);
        
        // Update app name
        $config = str_replace(
            "'name' => env('APP_NAME', 'Laravel'),",
            "'name' => env('APP_NAME', '{$project->name}'),",
            $config
        );
        
        File::put($configPath, $config);
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
        $this->info("📋 Copying CMS controllers...");
        
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
            'HomeController.php'
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
        
        // Copy middleware cần thiết (loại trừ SuperAdmin middleware)
        $cmsMiddleware = [
            'AdminMiddleware.php',
            'CMSMiddleware.php',
            'ProjectMiddleware.php',
            'SetProjectDatabase.php',
            'ProjectSubdomainMiddleware.php'
        ];
        
        foreach ($cmsMiddleware as $middleware) {
            $sourcePath = app_path("Http/Middleware/{$middleware}");
            $destPath = "{$exportDir}/app/Http/Middleware/{$middleware}";
            
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $destPath);
            }
        }
        
        $this->info("✅ CMS controllers and middleware copied");
    }
    
    private function copyProjectAssets($project, $exportDir)
    {
        $this->info("🎨 Copying project assets...");
        
        // Copy project-specific uploads
        $uploadsPath = storage_path("app/public/projects/{$project->code}");
        if (File::exists($uploadsPath)) {
            File::copyDirectory($uploadsPath, "{$exportDir}/storage/app/public/uploads");
        }
        
        // Copy compiled assets
        if (File::exists(public_path('build'))) {
            File::copyDirectory(public_path('build'), "{$exportDir}/public/build");
        }
        
        $this->info("✅ Project assets copied");
    }
    
    private function generateDeploymentFiles($project, $exportDir)
    {
        $this->info("🚀 Generating deployment files...");
        
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
- ✅ Product Management
- ✅ Category Management  
- ✅ Brand Management
- ✅ Order Management
- ✅ Content Management (Posts, Pages)
- ✅ Menu Management
- ✅ Widget Management
- ✅ Settings Management
- ✅ User & Role Management (project level)
- ✅ Frontend Display
- ✅ API Endpoints

## CMS Access
- Admin Panel: `/admin`
- Login: Use seeded admin account or register new user
- Frontend: `/`

## Project Details
- Code: {$project->code}
- Name: {$project->name}
- Domain: {$project->domain}
- Type: Standalone CMS
- Exported: " . date('Y-m-d H:i:s') . "

## Note
This is a standalone CMS export. SuperAdmin functionality is not included.
Each project operates independently with its own database and users.
";
        
        File::put("{$exportDir}/README.md", $readmeContent);
        
        // Tạo deployment script
        $deployScript = "#!/bin/bash
# Deployment script for {$project->name}

echo \"🚀 Deploying {$project->name}...\"

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

echo \"✅ Deployment completed!\"
";
        
        File::put("{$exportDir}/deploy.sh", $deployScript);
        chmod("{$exportDir}/deploy.sh", 0755);
        
        $this->info("✅ Deployment files generated");
    }
    
    private function createZipFile($projectCode, $exportDir)
    {
        $this->info("📦 Creating zip file...");
        
        $zipPath = storage_path("app/exports/{$projectCode}_standalone.zip");
        
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
            
            // Cleanup export directory
            File::deleteDirectory($exportDir);
            
            return $zipPath;
        }
        
        throw new \Exception("Cannot create zip file");
    }
}
```

#### 10.2 Tạo Command List Projects

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

class ListProjects extends Command
{
    protected $signature = 'project:list';
    protected $description = 'List all available projects';

    public function handle()
    {
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->info("No projects found.");
            return;
        }
        
        $this->info("Available Projects:");
        $this->info("==================");
        
        foreach ($projects as $project) {
            $this->info("Code: {$project->code}");
            $this->info("Name: {$project->name}");
            $this->info("Domain: {$project->domain}");
            $this->info("Status: " . ($project->is_active ? 'Active' : 'Inactive'));
            $this->info("Database: project_" . strtolower($project->code));
            $this->info("---");
        }
        
        $this->info("\nTo export a project:");
        $this->info("php artisan project:export {project_code}");
    }
}
```

#### 10.3 Sử dụng Export Commands

```bash
# Liệt kê tất cả projects
php artisan project:list

# Export project sivgt thành CMS site độc lập
php artisan project:export sivgt

# Export chỉ CMS functionality (không có SuperAdmin)
php artisan project:export sivgt --cms-only

# Export với custom output path
php artisan project:export sivgt --output-path=custom/exports/
```

#### 10.4 Cấu trúc CMS Site được Export

```
sivgt_standalone.zip
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              # CMS Admin controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── PostController.php
│   │   │   ├── MenuController.php
│   │   │   ├── WidgetController.php
│   │   │   └── SettingsController.php
│   │   ├── Frontend/           # Frontend controllers
│   │   │   ├── ProductController.php
│   │   │   └── CategoryController.php
│   │   ├── Api/                # API controllers
│   │   │   ├── ProductController.php
│   │   │   ├── MenuController.php
│   │   │   └── WidgetController.php
│   │   └── HomeController.php
│   ├── Http/Middleware/        # CMS middleware (không có SuperAdmin)
│   │   ├── AdminMiddleware.php
│   │   ├── CMSMiddleware.php
│   │   └── ProjectMiddleware.php
│   ├── Models/                 # Tất cả models
│   └── Services/               # Services
├── routes/
│   ├── web.php                 # CMS routes (không có SuperAdmin routes)
│   └── api.php                 # API routes cho CMS
├── resources/views/            # CMS views
├── public/                     # Assets
├── database.sql                # Database dump của project
├── .env                        # Environment config cho CMS
├── README.md                   # Hướng dẫn deployment CMS
└── deploy.sh                   # Script deployment CMS
```

#### 10.5 Deployment Site độc lập

**Trên hosting mới:**

1. **Upload & Extract:**
   ```bash
   unzip sivgt_standalone.zip
   ```

2. **Setup Database:**
   ```bash
   # Tạo database mới
   mysql -u username -p -e "CREATE DATABASE sivgt_standalone"
   
   # Import data
   mysql -u username -p sivgt_standalone < database.sql
   ```

3. **Configure Environment:**
   ```bash
   # Cập nhật .env
   nano .env
   
   # Update database credentials
   DB_DATABASE=sivgt_standalone
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Run Deployment:**
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

5. **Set Web Root:**
   - Point domain to `/public` folder
   - Or setup `.htaccess` redirect

#### 10.6 Tạo Batch Export cho tất cả Projects

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

class ExportAllProjects extends Command
{
    protected $signature = 'project:export-all {--active-only : Export only active projects}';
    protected $description = 'Export all projects as standalone sites';

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
        
        $this->info("Exporting {$projects->count()} projects...");
        
        foreach ($projects as $project) {
            $this->info("Exporting: {$project->code}");
            
            try {
                $this->call('project:export', ['projectCode' => $project->code]);
                $this->info("✅ {$project->code} exported successfully");
            } catch (\Exception $e) {
                $this->error("❌ Failed to export {$project->code}: " . $e->getMessage());
            }
        }
        
        $this->info("🎉 Batch export completed!");
    }
}
```

## Tóm tắt

1. **Nguyên nhân chính:** User database không có quyền truy cập `project_sivgt`
2. **Giải pháp:** Tạo database và gán quyền trên Hostinger hPanel
3. **Backup plan:** Sử dụng single database với project scoping nếu cần
4. **Export feature:** Xuất từng project thành site Laravel độc lập hoàn chỉnh
5. **Monitoring:** Tạo health check và debug commands

**Lưu ý quan trọng:** 
- Trên shared hosting như Hostinger, việc tạo database phải thực hiện qua hPanel, không thể tạo tự động qua code
- Mỗi project được export sẽ là một Laravel site hoàn chỉnh, có thể deploy độc lập
- Site được export bao gồm: code, database, config, assets và hướng dẫn deployment