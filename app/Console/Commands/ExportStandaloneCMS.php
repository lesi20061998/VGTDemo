<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ExportStandaloneCMS extends Command
{
    protected $signature = 'project:export-standalone {projectCode}';

    protected $description = 'Export standalone CMS (no projectCode dependency)';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');

        $project = Project::where('code', $projectCode)->first();

        if (! $project) {
            $this->error("Project with code '{$projectCode}' not found!");

            return 1;
        }

        $this->info("🚀 Exporting STANDALONE CMS: {$project->name} ({$projectCode})");

        $exportDir = storage_path("app/standalone-cms/{$projectCode}");
        $this->createExportDirectory($exportDir);

        // 1. Copy essential files
        $this->copyEssentialFiles($exportDir);

        // 2. Create standalone routes (OVERRIDE existing routes)
        $this->createStandaloneRoutes($project, $exportDir);

        // 3. Create standalone middleware
        $this->createStandaloneMiddleware($exportDir);

        // 4. Create standalone bootstrap
        $this->createStandaloneBootstrap($exportDir);

        // 5. Export database
        $this->exportDatabase($project, $exportDir);

        // 6. Create environment config
        $this->createEnvironmentConfig($project, $exportDir);

        // 7. Create documentation
        $this->createDocumentation($project, $exportDir);

        // 8. Create zip
        $zipPath = $this->createZip($projectCode, $exportDir);

        $this->info('✅ STANDALONE CMS exported!');
        $this->info("📦 Location: {$zipPath}");
        $this->info('🎉 Ready for deployment without projectCode dependency!');

        return 0;
    }

    private function createExportDirectory($exportDir)
    {
        if (File::exists($exportDir)) {
            File::deleteDirectory($exportDir);
        }
        File::makeDirectory($exportDir, 0755, true);
        $this->info('📁 Created export directory');
    }

    private function copyEssentialFiles($exportDir)
    {
        $this->info('📋 Copying essential files...');

        // Essential directories (EXCLUDE bootstrap to avoid overwriting)
        $dirs = ['app', 'config', 'database', 'resources', 'public'];

        foreach ($dirs as $dir) {
            if (File::exists(base_path($dir))) {
                $this->info("  📂 {$dir}");
                File::copyDirectory(base_path($dir), "{$exportDir}/{$dir}");
            }
        }

        // Copy bootstrap but exclude app.php
        if (File::exists(base_path('bootstrap'))) {
            $this->info('  📂 bootstrap (selective)');
            File::makeDirectory("{$exportDir}/bootstrap", 0755, true);

            // Copy bootstrap files except app.php
            $bootstrapFiles = File::files(base_path('bootstrap'));
            foreach ($bootstrapFiles as $file) {
                if ($file->getFilename() !== 'app.php') {
                    File::copy($file->getPathname(), "{$exportDir}/bootstrap/{$file->getFilename()}");
                }
            }

            // Copy bootstrap/cache directory if it exists
            if (File::exists(base_path('bootstrap/cache'))) {
                File::copyDirectory(base_path('bootstrap/cache'), "{$exportDir}/bootstrap/cache");
            } else {
                File::makeDirectory("{$exportDir}/bootstrap/cache", 0755, true);
            }
        }

        // Essential files
        $files = ['artisan', 'composer.json', 'composer.lock'];
        foreach ($files as $file) {
            if (File::exists(base_path($file))) {
                File::copy(base_path($file), "{$exportDir}/{$file}");
            }
        }

        // Create minimal structure (avoid duplicate bootstrap/cache)
        File::makeDirectory("{$exportDir}/storage/logs", 0755, true);
        File::makeDirectory("{$exportDir}/storage/app/public", 0755, true);
        File::makeDirectory("{$exportDir}/storage/framework/cache", 0755, true);
        File::makeDirectory("{$exportDir}/storage/framework/sessions", 0755, true);
        File::makeDirectory("{$exportDir}/storage/framework/views", 0755, true);
        File::makeDirectory("{$exportDir}/routes", 0755, true);

        $this->info('✅ Essential files copied (routes & bootstrap/app.php will be created separately)');
    }

    private function createStandaloneRoutes($project, $exportDir)
    {
        $this->info('🛣️  Creating standalone routes...');

        // Create routes/web.php (STANDALONE - no projectCode)
        $webRoutes = "<?php

use Illuminate\\Support\\Facades\\Route;
use App\\Http\\Controllers\\Admin\\DashboardController;
use App\\Http\\Controllers\\Admin\\ProductController;
use App\\Http\\Controllers\\Admin\\CategoryController;
use App\\Http\\Controllers\\Admin\\BrandController;
use App\\Http\\Controllers\\Admin\\OrderController;
use App\\Http\\Controllers\\Admin\\PostController;
use App\\Http\\Controllers\\Admin\\UserController;
use App\\Http\\Controllers\\Admin\\SettingsController;
use App\\Http\\Controllers\\Admin\\MenuController;
use App\\Http\\Controllers\\Admin\\WidgetController;
use App\\Http\\Controllers\\Frontend\\HomeController;
use App\\Http\\Controllers\\Frontend\\ProductController as FrontendProductController;
use App\\Http\\Controllers\\Frontend\\PostController as FrontendPostController;
use App\\Http\\Controllers\\Frontend\\PageController;
use App\\Http\\Controllers\\Frontend\\CartController;

/*
|--------------------------------------------------------------------------
| {$project->name} - Standalone CMS Routes
|--------------------------------------------------------------------------
| This is a standalone Laravel CMS with no project code dependencies.
| All routes work directly without any {projectCode} prefix.
*/

// ============================================
// FRONTEND ROUTES
// ============================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [FrontendProductController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [FrontendProductController::class, 'show'])->name('products.show');

// Blog
Route::get('/blog', [FrontendPostController::class, 'index'])->name('posts.index');
Route::get('/blog/{slug}', [FrontendPostController::class, 'show'])->name('posts.show');

// Pages
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');

// Cart & Checkout
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('checkout.process');
Route::get('/order/success', fn () => view('frontend.cart.success'))->name('order.success');

// ============================================
// AUTH ROUTES
// ============================================
Auth::routes();

// ============================================
// CMS ADMIN ROUTES (Standalone)
// ============================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products Management
    Route::resource('products', ProductController::class);
    Route::post('products/bulk-edit', [ProductController::class, 'bulkEdit'])->name('products.bulk-edit');
    Route::post('products/bulk-update', [ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::post('products/toggle-badge', [ProductController::class, 'toggleBadge'])->name('products.toggle-badge');
    
    // Categories & Brands
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
    Route::resource('brands', BrandController::class);
    
    // Posts Management (Blog)
    Route::resource('posts', PostController::class);
    
    // Pages Management
    Route::get('pages', [PostController::class, 'index'])->name('pages.index')->defaults('post_type', 'page');
    Route::get('pages/create', [PostController::class, 'create'])->name('pages.create')->defaults('type', 'page');
    Route::get('pages/{post}', [PostController::class, 'show'])->name('pages.show');
    Route::get('pages/{post}/edit', [PostController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{post}', [PostController::class, 'update'])->name('pages.update');
    Route::delete('pages/{post}', [PostController::class, 'destroy'])->name('pages.destroy');
    
    // Orders Management
    Route::get('orders/reports', [OrderController::class, 'reports'])->name('orders.reports');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Menu Management
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
    Route::put('menus/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
    Route::delete('menus/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
    Route::post('menus/{menu}/update-tree', [MenuController::class, 'updateTree'])->name('menus.update-tree');
    
    // Widget Management
    Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::post('widgets', [WidgetController::class, 'store'])->name('widgets.store');
    Route::delete('widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');
    Route::post('widgets/clear', fn () => \\App\\Models\\Widget::truncate())->name('widgets.clear');
    
    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Settings Pages
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('contact', fn () => view('cms.settings.contact'))->name('contact');
        Route::get('seo', fn () => view('cms.settings.seo'))->name('seo');
        Route::get('social', fn () => view('cms.settings.social'))->name('social');
        Route::get('payment', fn () => view('cms.settings.payment'))->name('payment');
        Route::get('shipping', fn () => view('cms.settings.shipping'))->name('shipping');
        Route::get('analytics', fn () => view('cms.settings.analytics'))->name('analytics');
    });
});

// Dynamic Pages (must be last to avoid conflicts)
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|login|logout|cart|checkout|products|product|blog|contact).*')
    ->name('pages.show');
";

        File::put("{$exportDir}/routes/web.php", $webRoutes);

        // Create routes/api.php
        $apiRoutes = "<?php

use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Route;

Route::get('/user', function (Request \$request) {
    return \$request->user();
})->middleware('auth:sanctum');

// CMS API routes
Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('products', [\\App\\Http\\Controllers\\Api\\ProductController::class, 'index'])->name('products.index');
    Route::get('categories', [\\App\\Http\\Controllers\\Api\\CategoryController::class, 'index'])->name('categories.index');
    Route::get('posts', [\\App\\Http\\Controllers\\Api\\PostController::class, 'index'])->name('posts.index');
    Route::get('menus', [\\App\\Http\\Controllers\\Api\\MenuController::class, 'index'])->name('menus.index');
    Route::get('widgets/{area}', [\\App\\Http\\Controllers\\Api\\WidgetController::class, 'getByArea'])->name('widgets.area');
});
";

        File::put("{$exportDir}/routes/api.php", $apiRoutes);

        // Create routes/console.php
        $consoleRoutes = "<?php

use Illuminate\\Foundation\\Inspiring;
use Illuminate\\Support\\Facades\\Artisan;

Artisan::command('inspire', function () {
    \$this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
";

        File::put("{$exportDir}/routes/console.php", $consoleRoutes);

        $this->info('✅ Standalone routes created');
    }

    private function createStandaloneMiddleware($exportDir)
    {
        $this->info('🛡️  Creating standalone middleware...');

        $middleware = "<?php

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Config;
use Symfony\\Component\\HttpFoundation\\Response;

/**
 * Standalone CMS Middleware
 * 
 * This middleware ensures the CMS runs as a standalone application
 * without any multi-tenant or project code dependencies.
 */
class StandaloneCMS
{
    public function handle(Request \$request, Closure \$next): Response
    {
        // Ensure we're using the default database connection
        Config::set('database.default', 'mysql');
        
        // Set standalone mode flag
        config(['app.standalone_cms' => true]);
        
        return \$next(\$request);
    }
}
";

        File::put("{$exportDir}/app/Http/Middleware/StandaloneCMS.php", $middleware);

        $this->info('✅ Standalone middleware created');
    }

    private function createStandaloneBootstrap($exportDir)
    {
        $this->info('⚡ Creating standalone bootstrap...');

        $bootstrap = "<?php

use Illuminate\\Foundation\\Application;
use Illuminate\\Foundation\\Configuration\\Exceptions;
use Illuminate\\Foundation\\Configuration\\Middleware;

/*
|--------------------------------------------------------------------------
| Standalone CMS Bootstrap
|--------------------------------------------------------------------------
| This bootstrap file configures the Laravel application to run as a
| standalone CMS without any multi-tenant or project code dependencies.
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware \$middleware) {
        // Add standalone CMS middleware
        \$middleware->web(append: [
            \\App\\Http\\Middleware\\StandaloneCMS::class,
        ]);
    })
    ->withExceptions(function (Exceptions \$exceptions) {
        //
    })->create();
";

        File::put("{$exportDir}/bootstrap/app.php", $bootstrap);

        $this->info('✅ Standalone bootstrap created');
    }

    private function exportDatabase($project, $exportDir)
    {
        $this->info('💾 Exporting database...');

        $dbName = 'project_'.strtolower($project->code);
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

            $sql = "-- Standalone CMS Database: {$dbName}\n-- Generated: ".date('Y-m-d H:i:s')."\n-- No project code dependencies\n\n";

            $tables = DB::connection('export_project')->select('SHOW TABLES');

            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];

                // Structure
                $createTable = DB::connection('export_project')->select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'}.";\n\n";

                // Data (limited to avoid huge files)
                $count = DB::connection('export_project')->table($tableName)->count();
                if ($count > 0 && $count < 1000) {
                    $rows = DB::connection('export_project')->table($tableName)->get();
                    if ($rows->count() > 0) {
                        $sql .= "-- Data for {$tableName}\n";
                        foreach ($rows->chunk(25) as $chunk) {
                            $values = [];
                            foreach ($chunk as $row) {
                                $rowData = array_map(fn ($value) => $value === null ? 'NULL' : "'".addslashes($value)."'", (array) $row);
                                $values[] = '('.implode(',', $rowData).')';
                            }
                            $sql .= "INSERT INTO `{$tableName}` VALUES ".implode(',', $values).";\n";
                        }
                        $sql .= "\n";
                    }
                } elseif ($count >= 1000) {
                    $sql .= "-- Table {$tableName} has {$count} rows - export manually if needed\n\n";
                }
            }

            File::put($sqlFile, $sql);
            $this->info('✅ Database exported');

        } catch (\Exception $e) {
            $this->error('Database export failed: '.$e->getMessage());
            File::put($sqlFile, "-- Database export failed\n-- Please export manually: mysqldump -u user -p {$dbName} > database.sql\n");
        }
    }

    private function createEnvironmentConfig($project, $exportDir)
    {
        $this->info('⚙️  Creating environment config...');

        $envContent = "APP_NAME=\"{$project->name}\"
APP_ENV=production
APP_KEY=".config('app.key')."
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE={$project->code}_standalone
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=\"hello@example.com\"
MAIL_FROM_NAME=\"{$project->name}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME=\"{$project->name}\"
VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
VITE_PUSHER_HOST=\"\${PUSHER_HOST}\"
VITE_PUSHER_PORT=\"\${PUSHER_PORT}\"
VITE_PUSHER_SCHEME=\"\${PUSHER_SCHEME}\"
VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"

# Standalone CMS Configuration
CMS_STANDALONE=true
CMS_NAME=\"{$project->name}\"
CMS_VERSION=1.0.0
";

        File::put("{$exportDir}/.env.example", $envContent);

        $this->info('✅ Environment config created');
    }

    private function createDocumentation($project, $exportDir)
    {
        $this->info('📚 Creating documentation...');

        $readme = "# {$project->name} - Standalone CMS

## 🎯 Overview

This is a **complete, standalone Laravel CMS** exported from a multi-tenant system. 
It runs **independently without any project code dependencies** - no more `/{projectCode}/` URLs!

## ✨ Key Features

### 🚫 No Project Code Dependencies
- **Before**: `https://domain.com/sivgt/admin` 
- **After**: `https://domain.com/admin` ✅

### 🛣️ Clean Routes
- Frontend: `https://your-domain.com/`
- Admin: `https://your-domain.com/admin`
- API: `https://your-domain.com/api/`

### 🎛️ Full CMS Features
- ✅ Product Management (Categories, Brands, Attributes)
- ✅ Content Management (Posts, Pages, Menus)
- ✅ E-commerce (Cart, Checkout, Orders)
- ✅ Widget System (Homepage customization)
- ✅ User Management & Settings
- ✅ SEO & Analytics Integration

## 🚀 Quick Installation

### 1. Extract & Setup
```bash
unzip {$project->code}_standalone.zip
cd {$project->code}_standalone/
composer install --no-dev --optimize-autoloader
```

### 2. Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database
```bash
# Create database
mysql -u root -p -e \"CREATE DATABASE {$project->code}_standalone\"

# Import data
mysql -u root -p {$project->code}_standalone < database.sql
```

### 4. Configure .env
```env
DB_DATABASE={$project->code}_standalone
DB_USERNAME=your_username
DB_PASSWORD=your_password
APP_URL=https://your-domain.com
```

### 5. Permissions & Cache
```bash
chmod -R 755 storage/ bootstrap/cache/
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🌐 Access Your CMS

- **Website**: `https://your-domain.com/`
- **Admin Panel**: `https://your-domain.com/admin`
- **Login**: Use credentials from imported database

## 🔧 Technical Details

### Requirements
- PHP 8.1+
- MySQL 5.7+
- Composer
- Web server (Apache/Nginx)

### Architecture
- **Framework**: Laravel ".app()->version()."
- **Database**: Single MySQL database
- **Sessions**: File-based (no multi-tenant complexity)
- **Routing**: Direct routes (no project code prefixes)

### What's Different from Multi-Tenant
- ❌ No `/{projectCode}/` URL prefixes
- ❌ No project database switching
- ❌ No multi-tenant middleware
- ✅ Direct, clean URLs
- ✅ Single database connection
- ✅ Simplified routing

## 📁 Project Structure

```
{$project->code}_standalone/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # CMS admin controllers
│   │   │   ├── Frontend/       # Website controllers
│   │   │   └── Api/           # API controllers
│   │   └── Middleware/
│   │       └── StandaloneCMS.php  # Standalone middleware
│   └── Models/                 # All CMS models
├── routes/
│   ├── web.php                # Main routes (no projectCode)
│   ├── api.php                # API routes
│   └── console.php            # Console commands
├── resources/views/           # All CMS views
├── database.sql              # Complete database dump
└── .env.example              # Environment template
```

## 🚀 Deployment

### Apache VirtualHost
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/{$project->code}_standalone/public
    
    <Directory /path/to/{$project->code}_standalone/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/{$project->code}_standalone/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php\$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🔒 Security Checklist

- [ ] Change default admin passwords
- [ ] Update APP_KEY in .env
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Configure SSL certificate
- [ ] Enable firewall rules
- [ ] Setup regular database backups
- [ ] Remove debug mode in production

## 📊 Export Information

- **Original Project**: {$project->name}
- **Project Code**: {$project->code}
- **Export Date**: ".date('Y-m-d H:i:s').'
- **Laravel Version**: '.app()->version().'
- **Export Type**: Standalone CMS (no project dependencies)

## 🎉 Success!

Your CMS is now completely standalone and ready for production!

**No more project codes, no more complex routing - just a clean, working Laravel CMS.**

---

*This CMS was exported from a multi-tenant system and converted to standalone mode.*
';

        File::put("{$exportDir}/README.md", $readme);

        $this->info('✅ Documentation created');
    }

    private function createZip($projectCode, $exportDir)
    {
        $this->info('📦 Creating zip...');

        $zipPath = storage_path("app/standalone-cms/{$projectCode}_standalone.zip");

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($exportDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $fileCount = 0;
            foreach ($iterator as $file) {
                if (! $file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($exportDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                    $fileCount++;
                }
            }

            $zip->close();
            File::deleteDirectory($exportDir);

            $this->info("✅ Zip created with {$fileCount} files");

            return $zipPath;
        }

        throw new \Exception('Cannot create zip file');
    }
}
