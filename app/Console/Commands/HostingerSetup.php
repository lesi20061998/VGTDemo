<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class HostingerSetup extends Command
{
    protected $signature = 'hostinger:setup {action} {--force}';
    protected $description = 'Setup and configure Laravel application for Hostinger deployment';

    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'check':
                return $this->checkEnvironment();
            case 'configure':
                return $this->configureForHostinger();
            case 'env':
                return $this->setupEnvironment();
            case 'permissions':
                return $this->checkPermissions();
            default:
                $this->showHelp();
        }
    }
    
    private function showHelp()
    {
        $this->info("🎯 Hostinger Setup Tool");
        $this->info("");
        $this->info("Available actions:");
        $this->info("  check        - Check current environment and configuration");
        $this->info("  configure    - Configure application for Hostinger");
        $this->info("  env          - Setup .env file for production");
        $this->info("  permissions  - Check file permissions");
        $this->info("");
        $this->info("Options:");
        $this->info("  --force      - Force operations without confirmation");
        $this->info("");
        $this->info("Examples:");
        $this->info("  php artisan hostinger:setup check");
        $this->info("  php artisan hostinger:setup configure");
        $this->info("  php artisan hostinger:setup env");
    }
    
    private function checkEnvironment()
    {
        $this->info("🔍 CHECKING HOSTINGER ENVIRONMENT");
        $this->info("=================================");
        $this->info("");
        
        // Check PHP version
        $phpVersion = PHP_VERSION;
        $this->info("🐘 PHP Version: {$phpVersion}");
        
        if (version_compare($phpVersion, '8.2', '>=')) {
            $this->info("   ✅ PHP version is compatible");
        } else {
            $this->warn("   ⚠️  PHP version should be 8.2 or higher");
        }
        
        // Check Laravel version
        $laravelVersion = app()->version();
        $this->info("🚀 Laravel Version: {$laravelVersion}");
        
        // Check environment
        $environment = app()->environment();
        $this->info("🌍 Environment: {$environment}");
        
        if ($environment === 'production') {
            $this->info("   ✅ Running in production mode");
        } else {
            $this->warn("   ⚠️  Not in production mode");
        }
        
        // Check database configuration
        $this->info("");
        $this->info("💾 DATABASE CONFIGURATION:");
        $dbHost = env('DB_HOST', 'localhost');
        $dbUsername = env('DB_USERNAME', 'root');
        $dbDatabase = env('DB_DATABASE', 'laravel');
        
        $this->info("   Host: {$dbHost}");
        $this->info("   Username: {$dbUsername}");
        $this->info("   Database: {$dbDatabase}");
        
        // Check if it looks like Hostinger
        if (preg_match('/^u\d+_/', $dbUsername)) {
            $this->info("   ✅ Hostinger database configuration detected");
            $userPrefix = explode('_', $dbUsername)[0];
            $this->info("   🔑 User Prefix: {$userPrefix}");
        } else {
            $this->warn("   ⚠️  Local development configuration detected");
        }
        
        // Check important directories
        $this->info("");
        $this->info("📁 DIRECTORY PERMISSIONS:");
        
        $directories = [
            'storage/app',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'bootstrap/cache'
        ];
        
        foreach ($directories as $dir) {
            $path = base_path($dir);
            if (is_dir($path) && is_writable($path)) {
                $this->info("   ✅ {$dir} - writable");
            } else {
                $this->error("   ❌ {$dir} - not writable or missing");
            }
        }
        
        // Check .env file
        $this->info("");
        $this->info("⚙️  CONFIGURATION FILES:");
        
        if (File::exists(base_path('.env'))) {
            $this->info("   ✅ .env file exists");
        } else {
            $this->error("   ❌ .env file missing");
        }
        
        if (File::exists(base_path('.env.example'))) {
            $this->info("   ✅ .env.example exists");
        } else {
            $this->warn("   ⚠️  .env.example missing");
        }
        
        // Check key configuration
        $appKey = env('APP_KEY');
        if ($appKey) {
            $this->info("   ✅ APP_KEY is set");
        } else {
            $this->error("   ❌ APP_KEY is not set");
        }
        
        // Check important settings
        $this->info("");
        $this->info("🔧 IMPORTANT SETTINGS:");
        
        $appDebug = env('APP_DEBUG', false);
        if ($appDebug) {
            $this->warn("   ⚠️  APP_DEBUG is enabled (should be false in production)");
        } else {
            $this->info("   ✅ APP_DEBUG is disabled");
        }
        
        $appUrl = env('APP_URL', 'http://localhost');
        $this->info("   🌐 APP_URL: {$appUrl}");
        
        return 0;
    }
    
    private function configureForHostinger()
    {
        $this->info("🔧 CONFIGURING FOR HOSTINGER");
        $this->info("============================");
        $this->info("");
        
        if (!$this->option('force')) {
            if (!$this->confirm('This will modify your application configuration. Continue?')) {
                $this->info("Operation cancelled.");
                return 0;
            }
        }
        
        // Update .htaccess for Hostinger
        $this->info("📝 Updating .htaccess file...");
        $this->updateHtaccess();
        
        // Create storage directories if missing
        $this->info("📁 Creating storage directories...");
        $this->createStorageDirectories();
        
        // Set proper permissions
        $this->info("🔐 Setting permissions...");
        $this->setPermissions();
        
        // Create database configuration helper
        $this->info("💾 Creating database configuration...");
        $this->createDatabaseConfig();
        
        $this->info("");
        $this->info("🎉 Configuration completed!");
        $this->info("");
        $this->warn("📝 NEXT STEPS:");
        $this->warn("1. Update your .env file with Hostinger database credentials");
        $this->warn("2. Run: php artisan hostinger:setup env");
        $this->warn("3. Create project databases in Hostinger hPanel");
        $this->warn("4. Test database connections");
        
        return 0;
    }
    
    private function setupEnvironment()
    {
        $this->info("⚙️  SETTING UP ENVIRONMENT");
        $this->info("==========================");
        $this->info("");
        
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');
        
        if (!File::exists($envExamplePath)) {
            $this->error("❌ .env.example file not found!");
            return 1;
        }
        
        // Read current .env if exists
        $currentEnv = [];
        if (File::exists($envPath)) {
            $currentEnv = $this->parseEnvFile($envPath);
        }
        
        // Read .env.example
        $exampleEnv = $this->parseEnvFile($envExamplePath);
        
        $this->info("📝 Current environment settings:");
        $this->info("");
        
        // Show important settings
        $importantKeys = [
            'APP_ENV',
            'APP_DEBUG',
            'APP_URL',
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME'
        ];
        
        foreach ($importantKeys as $key) {
            $current = $currentEnv[$key] ?? 'NOT SET';
            $example = $exampleEnv[$key] ?? 'NOT IN EXAMPLE';
            
            $this->info("   {$key}: {$current}");
        }
        
        $this->info("");
        
        if ($this->confirm('Would you like to update environment settings for Hostinger?')) {
            $this->updateEnvironmentForHostinger($currentEnv);
        }
        
        return 0;
    }
    
    private function checkPermissions()
    {
        $this->info("🔐 CHECKING FILE PERMISSIONS");
        $this->info("============================");
        $this->info("");
        
        $directories = [
            'storage' => 755,
            'storage/app' => 755,
            'storage/framework' => 755,
            'storage/framework/cache' => 755,
            'storage/framework/sessions' => 755,
            'storage/framework/views' => 755,
            'storage/logs' => 755,
            'bootstrap/cache' => 755,
        ];
        
        foreach ($directories as $dir => $expectedPerm) {
            $path = base_path($dir);
            
            if (!is_dir($path)) {
                $this->error("   ❌ {$dir} - directory missing");
                continue;
            }
            
            $currentPerm = substr(sprintf('%o', fileperms($path)), -3);
            $isWritable = is_writable($path);
            
            if ($isWritable) {
                $this->info("   ✅ {$dir} - writable (permissions: {$currentPerm})");
            } else {
                $this->error("   ❌ {$dir} - not writable (permissions: {$currentPerm})");
                
                if ($this->confirm("Fix permissions for {$dir}?")) {
                    chmod($path, octdec($expectedPerm));
                    $this->info("   🔧 Fixed permissions for {$dir}");
                }
            }
        }
        
        return 0;
    }
    
    private function updateHtaccess()
    {
        $htaccessPath = base_path('public/.htaccess');
        
        if (!File::exists($htaccessPath)) {
            $this->warn("   ⚠️  .htaccess file not found in public directory");
            return;
        }
        
        $content = File::get($htaccessPath);
        
        // Add Hostinger specific configurations
        $hostingerConfig = "
# Hostinger specific configurations
RewriteEngine On

# Handle Angular and Vue.js routes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^.*$ /index.php [L]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
</IfModule>

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
";
        
        if (!str_contains($content, 'Hostinger specific configurations')) {
            File::put($htaccessPath, $content . $hostingerConfig);
            $this->info("   ✅ Updated .htaccess with Hostinger configurations");
        } else {
            $this->info("   ✅ .htaccess already configured for Hostinger");
        }
    }
    
    private function createStorageDirectories()
    {
        $directories = [
            'storage/app/public',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'bootstrap/cache'
        ];
        
        foreach ($directories as $dir) {
            $path = base_path($dir);
            
            if (!is_dir($path)) {
                File::makeDirectory($path, 0755, true);
                $this->info("   ✅ Created directory: {$dir}");
            } else {
                $this->info("   ✅ Directory exists: {$dir}");
            }
        }
    }
    
    private function setPermissions()
    {
        $directories = [
            'storage' => 0755,
            'bootstrap/cache' => 0755,
        ];
        
        foreach ($directories as $dir => $perm) {
            $path = base_path($dir);
            
            if (is_dir($path)) {
                chmod($path, $perm);
                $this->info("   ✅ Set permissions for {$dir}");
            }
        }
    }
    
    private function createDatabaseConfig()
    {
        $configPath = base_path('config/hostinger.php');
        
        $config = "<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hostinger Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to Hostinger hosting environment
    |
    */

    'database_prefix' => env('HOSTINGER_DB_PREFIX', 'u712054581'),
    
    'project_database_format' => '{prefix}_{project_code}',
    
    'auto_create_databases' => env('HOSTINGER_AUTO_CREATE_DB', false),
    
    'default_permissions' => [
        'storage' => 0755,
        'bootstrap_cache' => 0755,
    ],
    
    'security' => [
        'force_https' => env('HOSTINGER_FORCE_HTTPS', true),
        'disable_debug' => env('HOSTINGER_DISABLE_DEBUG', true),
    ],
];
";
        
        File::put($configPath, $config);
        $this->info("   ✅ Created Hostinger configuration file");
    }
    
    private function parseEnvFile($path)
    {
        $env = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value, '"\'');
            }
        }
        
        return $env;
    }
    
    private function updateEnvironmentForHostinger($currentEnv)
    {
        $this->info("🔧 Updating environment for Hostinger...");
        
        $updates = [];
        
        // Ask for database credentials
        $dbUsername = $this->ask('Database Username (e.g., u712054581_VGTApp)', $currentEnv['DB_USERNAME'] ?? '');
        $dbPassword = $this->secret('Database Password');
        $dbHost = $this->ask('Database Host', $currentEnv['DB_HOST'] ?? 'localhost');
        $appUrl = $this->ask('Application URL (e.g., https://yourdomain.com)', $currentEnv['APP_URL'] ?? '');
        
        $updates['DB_USERNAME'] = $dbUsername;
        $updates['DB_PASSWORD'] = $dbPassword;
        $updates['DB_HOST'] = $dbHost;
        $updates['APP_URL'] = $appUrl;
        $updates['APP_ENV'] = 'production';
        $updates['APP_DEBUG'] = 'false';
        
        // Extract user prefix for database naming
        if (preg_match('/^(u\d+)_/', $dbUsername, $matches)) {
            $userPrefix = $matches[1];
            $updates['HOSTINGER_DB_PREFIX'] = $userPrefix;
        }
        
        // Update .env file
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';
        
        foreach ($updates as $key => $value) {
            if (str_contains($envContent, $key . '=')) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }
        
        File::put($envPath, $envContent);
        
        $this->info("   ✅ Environment file updated");
        $this->warn("   ⚠️  Please restart your web server to apply changes");
    }
}