<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use ZipArchive;

class ExportLightCMS extends Command
{
    protected $signature = 'project:export-light {projectCode}';
    protected $description = 'Export lightweight CMS (essential files only)';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("Project with code '{$projectCode}' not found!");
            return 1;
        }
        
        $this->info("🚀 Exporting LIGHT CMS: {$project->name} ({$projectCode})");
        
        $exportDir = storage_path("app/light-cms/{$projectCode}");
        $this->createExportDirectory($exportDir);
        
        // 1. Copy essential files only
        $this->copyEssentialFiles($exportDir);
        
        // 2. Export database
        $this->exportDatabase($project, $exportDir);
        
        // 3. Create config (AFTER copying files to override)
        $this->createConfig($project, $exportDir);
        
        // 4. Create zip
        $zipPath = $this->createZip($projectCode, $exportDir);
        
        $this->info("✅ LIGHT CMS exported!");
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
    
    private function copyEssentialFiles($exportDir)
    {
        $this->info("📋 Copying essential files...");
        
        // Essential directories
        $dirs = ['app', 'config', 'database', 'resources', 'routes'];
        
        foreach ($dirs as $dir) {
            if (File::exists(base_path($dir))) {
                $this->info("  📂 {$dir}");
                File::copyDirectory(base_path($dir), "{$exportDir}/{$dir}");
            }
        }
        
        // Essential files
        $files = ['artisan', 'composer.json', 'composer.lock'];
        foreach ($files as $file) {
            if (File::exists(base_path($file))) {
                File::copy(base_path($file), "{$exportDir}/{$file}");
            }
        }
        
        // Create minimal structure
        File::makeDirectory("{$exportDir}/storage/logs", 0755, true);
        File::makeDirectory("{$exportDir}/storage/app/public", 0755, true);
        File::makeDirectory("{$exportDir}/storage/framework/cache", 0755, true);
        File::makeDirectory("{$exportDir}/storage/framework/sessions", 0755, true);
        File::makeDirectory("{$exportDir}/storage/framework/views", 0755, true);
        File::makeDirectory("{$exportDir}/bootstrap/cache", 0755, true);
        File::makeDirectory("{$exportDir}/public", 0755, true);
        
        // Copy public essentials
        if (File::exists(public_path('index.php'))) {
            File::copy(public_path('index.php'), "{$exportDir}/public/index.php");
        }
        if (File::exists(public_path('.htaccess'))) {
            File::copy(public_path('.htaccess'), "{$exportDir}/public/.htaccess");
        }
        
        $this->info("✅ Essential files copied");
    }
    
    private function exportDatabase($project, $exportDir)
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
            
            $sql = "-- CMS Database: {$dbName}\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
            
            $tables = DB::connection('export_project')->select('SHOW TABLES');
            
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                // Structure
                $createTable = DB::connection('export_project')->select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                // Data (limited to avoid huge files)
                $count = DB::connection('export_project')->table($tableName)->count();
                if ($count > 0 && $count < 1000) { // Only export small tables
                    $rows = DB::connection('export_project')->table($tableName)->get();
                    if ($rows->count() > 0) {
                        $sql .= "-- Data for {$tableName}\n";
                        foreach ($rows->chunk(25) as $chunk) {
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
                } elseif ($count >= 1000) {
                    $sql .= "-- Table {$tableName} has {$count} rows - export manually if needed\n\n";
                }
            }
            
            File::put($sqlFile, $sql);
            $this->info("✅ Database exported");
            
        } catch (\Exception $e) {
            $this->error("Database export failed: " . $e->getMessage());
            File::put($sqlFile, "-- Database export failed\n-- Please export manually: mysqldump -u user -p {$dbName} > database.sql\n");
        }
    }
    
    private function createConfig($project, $exportDir)
    {
        $this->info("⚙️  Creating config...");
        
        // .env for standalone CMS
        $envContent = "APP_NAME=\"{$project->name}\"
APP_ENV=production
APP_KEY=" . config('app.key') . "
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE={$project->code}_cms
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

# Standalone CMS Settings
CMS_NAME=\"{$project->name}\"
CMS_VERSION=1.0.0
CMS_STANDALONE=true
";
        
        File::put("{$exportDir}/.env.example", $envContent);
        
        // Create standalone routes/web.php (no projectCode dependency)
        $routes = "<?php

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

// {$project->name} - Standalone CMS

// ============================================
// FRONTEND ROUTES (Website khách hàng)
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

// Dynamic Pages (must be last)
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');

// ============================================
// AUTH ROUTES
// ============================================
Auth::routes();

// ============================================
// CMS ADMIN ROUTES (Standalone - no projectCode)
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

// ============================================
// API ROUTES
// ============================================
Route::prefix('api')->name('api.')->group(function () {
    Route::get('products', [\\App\\Http\\Controllers\\Api\\ProductController::class, 'index'])->name('products.index');
    Route::get('categories', [\\App\\Http\\Controllers\\Api\\CategoryController::class, 'index'])->name('categories.index');
    Route::get('menus', [\\App\\Http\\Controllers\\Api\\MenuController::class, 'index'])->name('menus.index');
    Route::get('widgets/{area}', [\\App\\Http\\Controllers\\Api\\WidgetController::class, 'getByArea'])->name('widgets.area');
});
";
        
        File::put("{$exportDir}/routes/web.php", $routes);
        
        // Create standalone middleware (no project switching)
        $standaloneMiddleware = "<?php

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Config;
use Symfony\\Component\\HttpFoundation\\Response;

class StandaloneCMS
{
    public function handle(Request \$request, Closure \$next): Response
    {
        // Set single database connection for standalone CMS
        Config::set('database.default', 'mysql');
        
        return \$next(\$request);
    }
}
";
        
        File::put("{$exportDir}/app/Http/Middleware/StandaloneCMS.php", $standaloneMiddleware);
        
        // Create standalone bootstrap/app.php
        $bootstrapApp = "<?php

use Illuminate\\Foundation\\Application;
use Illuminate\\Foundation\\Configuration\\Exceptions;
use Illuminate\\Foundation\\Configuration\\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware \$middleware) {
        \$middleware->web(append: [
            \\App\\Http\\Middleware\\StandaloneCMS::class,
        ]);
    })
    ->withExceptions(function (Exceptions \$exceptions) {
        //
    })->create();
";
        
        File::put("{$exportDir}/bootstrap/app.php", $bootstrapApp);
        
        // Create simple API routes
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
        
        // Create console routes
        $consoleRoutes = "<?php

use Illuminate\\Foundation\\Inspiring;
use Illuminate\\Support\\Facades\\Artisan;

Artisan::command('inspire', function () {
    \$this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
";
        
        File::put("{$exportDir}/routes/console.php", $consoleRoutes);
        
        // README for standalone CMS
        $readme = "# {$project->name} - Standalone CMS

## 🚀 Standalone Laravel CMS

This is a complete, standalone Laravel CMS exported from the multi-tenant system.
It runs independently without any project code dependencies.

## 📋 Installation Guide

### 1. Extract Files
```bash
unzip {$project->code}_light_cms.zip
cd {$project->code}_cms/
```

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
```bash
# Create database
mysql -u root -p -e \"CREATE DATABASE {$project->code}_cms\"

# Import database
mysql -u root -p {$project->code}_cms < database.sql
```

### 5. Update .env File
Edit `.env` file with your database credentials:
```env
DB_DATABASE={$project->code}_cms
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
APP_URL=https://your-domain.com
```

### 6. Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/
```

### 7. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🎯 CMS Features

### ✅ Content Management
- **Products**: Full product catalog with categories, brands, attributes
- **Blog**: Posts and pages management
- **Menus**: Dynamic menu builder
- **Widgets**: Flexible widget system for homepage customization

### ✅ E-commerce Features
- **Shopping Cart**: Add to cart, checkout process
- **Orders**: Order management and tracking
- **Categories & Brands**: Organized product structure

### ✅ Administration
- **Dashboard**: Analytics and overview
- **User Management**: Admin users and roles
- **Settings**: Site configuration, SEO, social media
- **Media Manager**: File and image management

### ✅ Frontend Features
- **Responsive Design**: Mobile-friendly interface
- **Product Catalog**: Browse and search products
- **Blog System**: News and articles
- **Contact Forms**: Customer inquiries
- **SEO Optimized**: Meta tags, sitemaps

## 🌐 Access Points

- **Frontend**: `https://your-domain.com/`
- **Admin Panel**: `https://your-domain.com/admin`
- **API Endpoints**: `https://your-domain.com/api/`

## 👤 Default Admin Access

After importing the database, you can login with:
- **URL**: `/admin`
- **Username**: Check the `users` table in database
- **Password**: Default password from exported data

## 🔧 Technical Requirements

- **PHP**: 8.1 or higher
- **MySQL**: 5.7 or higher
- **Composer**: Latest version
- **Web Server**: Apache/Nginx with mod_rewrite

## 📁 Directory Structure

```
{$project->code}_cms/
├── app/                    # Application logic
├── bootstrap/              # Framework bootstrap
├── config/                 # Configuration files
├── database/               # Migrations and seeders
├── public/                 # Web accessible files
├── resources/              # Views, assets, lang files
├── routes/                 # Route definitions
├── storage/                # Logs, cache, uploads
├── vendor/                 # Composer dependencies
├── .env.example           # Environment template
├── database.sql           # Database dump
└── README.md              # This file
```

## 🚀 Deployment Notes

### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/{$project->code}_cms/public
    
    <Directory /path/to/{$project->code}_cms/public>
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
    root /path/to/{$project->code}_cms/public;
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
- [ ] Set proper file permissions
- [ ] Configure SSL certificate
- [ ] Enable firewall rules
- [ ] Regular database backups

## 📞 Support

This is a standalone CMS system exported from a multi-tenant Laravel application.
- **Project**: {$project->name}
- **Code**: {$project->code}
- **Export Date**: " . date('Y-m-d H:i:s') . "
- **Laravel Version**: " . app()->version() . "

## 🎉 Ready to Use!

Your standalone CMS is ready for production deployment.
No project codes, no multi-tenant complexity - just a clean, working Laravel CMS.
";
        
        File::put("{$exportDir}/README.md", $readme);
        
        $this->info("✅ Config created");
    }
    
    private function createZip($projectCode, $exportDir)
    {
        $this->info("📦 Creating zip...");
        
        $zipPath = storage_path("app/light-cms/{$projectCode}_light_cms.zip");
        
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