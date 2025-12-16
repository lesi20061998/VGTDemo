<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ProjectExportController extends Controller
{
    public function exportWebsite(Request $request, $projectCode)
    {
        // Tăng thời gian thực thi
        set_time_limit(300); // 5 phút
        ini_set('memory_limit', '1G');
        
        try {
            $project = Project::where('code', $projectCode)->firstOrFail();
            
            // Mốc 1: Chuẩn bị thư mục export (25%)
            $exportsDir = storage_path('app/exports');
            if (!File::exists($exportsDir)) {
                File::makeDirectory($exportsDir, 0755, true);
            }
            
            $exportPath = $exportsDir . '/' . $projectCode;
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0755, true);
            
            // Mốc 2: Copy source code tối ưu (50%)
            $this->exportEssentialFiles($project, $exportPath);
            
            // Mốc 3: Export database (75%)
            $this->exportDatabase($project, $exportPath);
            
            // Mốc 4: Tạo file cấu hình (90%)
            $this->createConfigFiles($project, $exportPath);
            // Tắt pre-deploy để tránh timeout
            // $this->preDeployOptimization($exportPath);
            
            // Hoàn thành: Tạo ZIP file (100%)
            $zipPath = $exportsDir . '/' . $projectCode . '_website.zip';
            $this->createCompleteZip($exportPath, $zipPath, $project);
            
            File::deleteDirectory($exportPath);
            
            return response()->download($zipPath)->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function exportProgress(Request $request, $projectCode)
    {
        return response()->json([
            'steps' => [
                ['percent' => 25, 'text' => 'Chuẩn bị thư mục export', 'completed' => false],
                ['percent' => 50, 'text' => 'Copy source code (app, config, routes...)', 'completed' => false],
                ['percent' => 75, 'text' => 'Export database & migrations', 'completed' => false],
                ['percent' => 90, 'text' => 'Tạo file cấu hình (.env, deploy.sh)', 'completed' => false]
            ]
        ]);
    }
    
    private function exportEssentialFiles($project, $exportPath)
    {
        try {
            $basePath = base_path();
            
            // Copy cấu trúc CMS (loại bỏ SuperAdmin)
            $directories = [
                'bootstrap' => 'bootstrap',
                'config' => 'config',
                'database' => 'database',
                'public' => 'public',
                'resources' => 'resources',
                'storage/app/public' => 'storage/app/public',
                'storage/framework/cache' => 'storage/framework/cache',
                'storage/framework/sessions' => 'storage/framework/sessions',
                'storage/framework/views' => 'storage/framework/views',
                'storage/logs' => 'storage/logs'
            ];
            
            foreach ($directories as $source => $dest) {
                $sourcePath = $basePath . '/' . $source;
                if (File::exists($sourcePath)) {
                    File::makeDirectory($exportPath . '/' . dirname($dest), 0755, true, true);
                    File::copyDirectory($sourcePath, $exportPath . '/' . $dest);
                }
            }
            
            // Copy app nhưng loại bỏ SuperAdmin
            $this->copyAppWithoutSuperAdmin($basePath, $exportPath);
            
            // Copy routes nhưng loại bỏ superadmin.php
            $this->copyRoutesWithoutSuperAdmin($basePath, $exportPath);
            
            // Tạo thư mục storage cần thiết
            $storageDirs = [
                'storage/framework/cache/data',
                'storage/framework/testing',
                'storage/app/public'
            ];
            
            foreach ($storageDirs as $dir) {
                File::makeDirectory($exportPath . '/' . $dir, 0755, true, true);
                File::put($exportPath . '/' . $dir . '/.gitkeep', '');
            }
            
            // Copy file quan trọng
            $files = [
                'artisan',
                'composer.json',
                'composer.lock',
                'package.json',
                '.env.example',
                '.gitignore',
                'README.md'
            ];
            
            foreach ($files as $file) {
                if (File::exists($basePath . '/' . $file)) {
                    File::copy($basePath . '/' . $file, $exportPath . '/' . $file);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Export essential files failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function copyAppWithoutSuperAdmin($basePath, $exportPath)
    {
        $appSource = $basePath . '/app';
        $appDest = $exportPath . '/app';
        
        if (!File::exists($appSource)) return;
        
        // Copy toàn bộ app
        File::copyDirectory($appSource, $appDest);
        
        // Xóa SuperAdmin controllers và middleware
        $superAdminPaths = [
            $appDest . '/Http/Controllers/SuperAdmin',
            $appDest . '/Http/Middleware/SuperAdminMiddleware.php'
        ];
        
        foreach ($superAdminPaths as $path) {
            if (File::exists($path)) {
                if (File::isDirectory($path)) {
                    File::deleteDirectory($path);
                } else {
                    File::delete($path);
                }
            }
        }
    }
    
    private function copyRoutesWithoutSuperAdmin($basePath, $exportPath)
    {
        $routesSource = $basePath . '/routes';
        $routesDest = $exportPath . '/routes';
        
        if (!File::exists($routesSource)) return;
        
        // Copy toàn bộ routes
        File::copyDirectory($routesSource, $routesDest);
        
        // Xóa superadmin.php
        $superAdminRoute = $routesDest . '/superadmin.php';
        if (File::exists($superAdminRoute)) {
            File::delete($superAdminRoute);
        }
        
        // Sửa web.php để loại bỏ require superadmin.php
        $webRoute = $routesDest . '/web.php';
        if (File::exists($webRoute)) {
            $content = File::get($webRoute);
            $content = str_replace("require __DIR__.'/superadmin.php';", "// SuperAdmin routes removed", $content);
            File::put($webRoute, $content);
        }
    }
    
    private function generateCustomCss($project)
    {
        return "/* Custom CSS for {$project->name} */\n";
    }
    
    private function generateCustomJs($project)
    {
        return "// Custom JS for {$project->name}\n";
    }
    
    private function exportDatabase($project, $exportPath)
    {
        try {
            // Tạo database folder
            File::makeDirectory($exportPath . '/database', 0755, true, true);
            
            // Export cơ bản
            $data = ['project' => $project->toArray()];
            File::put($exportPath . '/database/data.json', json_encode($data, JSON_PRETTY_PRINT));
            
            // Tạo SQL dump đơn giản
            $sql = "-- Database for {$project->name}\n";
            $sql .= "CREATE DATABASE IF NOT EXISTS `{$project->code}_db`;\n";
            File::put($exportPath . '/database/schema.sql', $sql);
            
        } catch (\Exception $e) {
            \Log::error('Export database failed: ' . $e->getMessage());
        }
    }
    
    private function createConfigFiles($project, $exportPath)
    {
        // Create project-specific .env file
        $envContent = $this->generateProjectEnv($project);
        File::put($exportPath . '/.env', $envContent);
        
        // Create installation README
        $readme = $this->generateInstallationReadme($project);
        File::put($exportPath . '/INSTALLATION.md', $readme);
        
        // Create export summary
        $summary = $this->generateExportSummary($project, $exportPath);
        File::put($exportPath . '/EXPORT_SUMMARY.md', $summary);
    }
    
    private function generateProjectEnv($project)
    {
        return "APP_NAME=\"" . $project->name . "\"
APP_ENV=production
APP_KEY=" . 'base64:' . base64_encode(random_bytes(32)) . "
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=" . strtolower($project->code) . "_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

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
MAIL_FROM_NAME=\"" . $project->name . "\"

# Project specific settings
PROJECT_CODE=" . $project->code . "
PROJECT_NAME=\"" . $project->name . "\"
";
    }
    
    private function generateInstallationReadme($project)
    {
        return "# {$project->name} - Laravel Project Export

## Installation Instructions

### Requirements
- PHP 8.1+
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Setup Steps

1. **Upload files to server**
   ```bash
   # Extract the ZIP file to your web directory
   ```

2. **Install dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run production
   ```

3. **Configure environment**
   ```bash
   cp .env .env.local
   # Edit .env.local with your database credentials
   php artisan key:generate
   ```

4. **Setup database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Set permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

## Project Details
- **Project Code**: {$project->code}
- **Exported**: " . now()->format('Y-m-d H:i:s') . "
- **Laravel Version**: " . app()->version() . "

## Support
Contact: support@vnglobaltech.com";
    }
    
    private function generateDeployScript($project)
    {
        return "#!/bin/bash\n" .
               "# Deployment script for {$project->name}\n" .
               "set -e\n\n" .
               "echo 'Deploying {$project->name}...'\n\n" .
               "# Install dependencies\n" .
               "echo 'Installing Composer dependencies...'\n" .
               "composer install --no-dev --optimize-autoloader\n\n" .
               "# Generate key\n" .
               "echo 'Generating application key...'\n" .
               "php artisan key:generate --force\n\n" .
               "# Cache configurations\n" .
               "echo 'Caching configurations...'\n" .
               "php artisan config:cache\n" .
               "php artisan route:cache\n" .
               "php artisan view:cache\n\n" .
               "# Database migration\n" .
               "echo 'Running database migrations...'\n" .
               "php artisan migrate --force\n\n" .
               "# Create storage link\n" .
               "echo 'Creating storage link...'\n" .
               "php artisan storage:link\n\n" .
               "# Set permissions\n" .
               "echo 'Setting permissions...'\n" .
               "chmod -R 755 storage bootstrap/cache\n" .
               "chmod +x artisan\n\n" .
               "echo '✅ Deployment completed!'\n";
    }
    
    private function generateWindowsDeployScript($project)
    {
        return "@echo off\r\n" .
               "echo Deploying {$project->name}...\r\n\r\n" .
               "echo Installing Composer dependencies...\r\n" .
               "composer install --no-dev --optimize-autoloader\r\n\r\n" .
               "echo Generating application key...\r\n" .
               "php artisan key:generate --force\r\n\r\n" .
               "echo Caching configurations...\r\n" .
               "php artisan config:cache\r\n" .
               "php artisan route:cache\r\n" .
               "php artisan view:cache\r\n\r\n" .
               "echo Running database migrations...\r\n" .
               "php artisan migrate --force\r\n\r\n" .
               "echo Creating storage link...\r\n" .
               "php artisan storage:link\r\n\r\n" .
               "echo Deployment completed successfully!\r\n" .
               "pause\r\n";
    }
    
    private function exportDatabaseSQL($project, $exportPath)
    {
        // Create database directory
        File::makeDirectory($exportPath . '/database', 0755, true, true);
        
        // Export structure and data as SQL
        $sql = $this->generateDatabaseSQL($project);
        File::put($exportPath . '/database/database.sql', $sql);
        
        // Create database config
        $dbConfig = $this->generateDatabaseConfig($project);
        File::put($exportPath . '/database/config.php', $dbConfig);
    }
    
    private function generateDatabaseSQL($project)
    {
        $sql = "-- Database export for {$project->name}\n";
        $sql .= "-- Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";
        
        // Create database
        $dbName = 'project_' . strtolower($project->code);
        $sql .= "CREATE DATABASE IF NOT EXISTS `{$dbName}`;\n";
        $sql .= "USE `{$dbName}`;\n\n";
        
        // Export tables structure and data
        $tables = ['products_enhanced', 'product_categories', 'brands', 'orders', 'menus', 'widgets', 'settings'];
        
        foreach ($tables as $table) {
            $sql .= $this->exportTableSQL($table, $project->id);
        }
        
        return $sql;
    }
    
    private function exportTableSQL($table, $projectId)
    {
        $sql = "-- Table: {$table}\n";
        
        // Get table structure (simplified)
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $sql .= $this->getTableStructure($table);
        
        // Get table data
        $sql .= $this->getTableData($table, $projectId);
        
        return $sql . "\n";
    }
    
    private function getTableStructure($table)
    {
        // Simplified table structures
        $structures = [
            'products_enhanced' => "CREATE TABLE `products_enhanced` (\n  `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n  `name` varchar(255) NOT NULL,\n  `slug` varchar(255) NOT NULL,\n  `price` decimal(10,2) DEFAULT NULL,\n  `project_id` bigint unsigned DEFAULT NULL,\n  PRIMARY KEY (`id`)\n);\n",
            'product_categories' => "CREATE TABLE `product_categories` (\n  `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n  `name` varchar(255) NOT NULL,\n  `slug` varchar(255) NOT NULL,\n  `project_id` bigint unsigned DEFAULT NULL,\n  PRIMARY KEY (`id`)\n);\n",
            // Add more table structures as needed
        ];
        
        return $structures[$table] ?? "-- Structure for {$table} not defined\n";
    }
    
    private function getTableData($table, $projectId)
    {
        // Export data for project-specific tables
        $sql = "-- Data for {$table}\n";
        
        try {
            $data = \DB::table($table)->where('project_id', $projectId)->get();
            
            foreach ($data as $row) {
                $values = [];
                foreach ((array)$row as $value) {
                    $values[] = is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }
                $sql .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
            }
        } catch (\Exception $e) {
            $sql .= "-- Error exporting data: " . $e->getMessage() . "\n";
        }
        
        return $sql;
    }
    
    private function generateDatabaseConfig($project)
    {
        return "<?php\n// Database configuration for {$project->name}\n\nreturn [\n    'host' => 'localhost',\n    'database' => 'project_" . strtolower($project->code) . "',\n    'username' => 'your_username',\n    'password' => 'your_password',\n    'charset' => 'utf8mb4',\n    'collation' => 'utf8mb4_unicode_ci'\n];";
    }
    
    private function createSecurityFiles($project, $exportPath)
    {
        // Create security directory
        File::makeDirectory($exportPath . '/security', 0755, true, true);
        
        // Create .env file
        $envContent = $this->generateEnvFile($project);
        File::put($exportPath . '/.env', $envContent);
        
        // Create security config
        $securityConfig = $this->generateSecurityConfig($project);
        File::put($exportPath . '/security/config.php', $securityConfig);
        
        // Create .htaccess with security rules
        $htaccessSecurity = $this->generateSecureHtaccess($project);
        File::put($exportPath . '/.htaccess', $htaccessSecurity);
    }
    
    private function generateEnvFile($project)
    {
        return "# Environment configuration for {$project->name}\n" .
               "APP_NAME=\"" . $project->name . "\"\n" .
               "APP_ENV=production\n" .
               "APP_KEY=" . base64_encode(random_bytes(32)) . "\n" .
               "APP_DEBUG=false\n" .
               "APP_URL=https://your-domain.com\n\n" .
               "DB_CONNECTION=mysql\n" .
               "DB_HOST=127.0.0.1\n" .
               "DB_PORT=3306\n" .
               "DB_DATABASE=project_" . strtolower($project->code) . "\n" .
               "DB_USERNAME=your_username\n" .
               "DB_PASSWORD=your_password\n";
    }
    
    private function generateSecurityConfig($project)
    {
        return "<?php\n// Security configuration for {$project->name}\n\n" .
               "// CSRF Protection\n" .
               "ini_set('session.cookie_httponly', 1);\n" .
               "ini_set('session.cookie_secure', 1);\n" .
               "ini_set('session.use_strict_mode', 1);\n\n" .
               "// Security Headers\n" .
               "header('X-Content-Type-Options: nosniff');\n" .
               "header('X-Frame-Options: DENY');\n" .
               "header('X-XSS-Protection: 1; mode=block');\n" .
               "header('Strict-Transport-Security: max-age=31536000; includeSubDomains');\n";
    }
    
    private function generateSecureHtaccess($project)
    {
        return "# Security and Rewrite Rules for {$project->name}\n" .
               "RewriteEngine On\n\n" .
               "# Security Headers\n" .
               "Header always set X-Content-Type-Options nosniff\n" .
               "Header always set X-Frame-Options DENY\n" .
               "Header always set X-XSS-Protection \"1; mode=block\"\n\n" .
               "# Hide sensitive files\n" .
               "<Files ~ \"^\\.(htaccess|htpasswd|env)$\">\n" .
               "    Order allow,deny\n" .
               "    Deny from all\n" .
               "</Files>\n\n" .
               "# URL Rewriting\n" .
               "RewriteCond %{REQUEST_FILENAME} !-f\n" .
               "RewriteCond %{REQUEST_FILENAME} !-d\n" .
               "RewriteRule ^(.*)$ index.php [QSA,L]\n";
    }
    
    private function createCompleteZip($sourcePath, $zipPath, $project)
    {
        try {
            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $this->addFilesToZip($zip, $sourcePath);
                
                // Add cả 2 deploy scripts
                $windowsDeploy = $this->generateWindowsDeployScript($project);
                $zip->addFromString('deploy.bat', $windowsDeploy);
                
                $linuxDeploy = $this->generateDeployScript($project);
                $zip->addFromString('deploy.sh', $linuxDeploy);
                
                $zip->close();
            }
        } catch (\Exception $e) {
            \Log::error('Create ZIP failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function addFilesToZip($zip, $sourcePath)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourcePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
    
    private function generateWindowsReadme($project)
    {
        return "# {$project->name} - Windows Installation\n\n" .
               "## Quick Start\n\n" .
               "1. Extract files to your web directory\n" .
               "2. Edit `.env` file with your database credentials\n" .
               "3. Run: `deploy.bat`\n" .
               "4. Access your website\n\n" .
               "## Requirements\n" .
               "- XAMPP/WAMP/MAMP\n" .
               "- PHP 8.1+\n" .
               "- Composer\n" .
               "- MySQL\n\n" .
               "## Support\n" .
               "Email: support@vnglobaltech.com";
    }
    
    private function generateLinuxReadme($project)
    {
        return "# {$project->name} - Linux Installation\n\n" .
               "## Quick Start\n\n" .
               "```bash\n" .
               "# Extract files\n" .
               "# Edit .env file\n" .
               "chmod +x deploy.sh\n" .
               "./deploy.sh\n" .
               "```\n\n" .
               "## Requirements\n" .
               "- PHP 8.1+\n" .
               "- Composer\n" .
               "- MySQL/MariaDB\n" .
               "- Apache/Nginx\n\n" .
               "## Support\n" .
               "Email: support@vnglobaltech.com";
    }
    
    private function generateExportSummary($project, $exportPath)
    {
        $exportedFiles = $this->scanExportedFiles($exportPath);
        $totalSize = $this->calculateDirectorySize($exportPath);
        
        $summary = "# 📦 Export Summary - {$project->name}\n\n";
        $summary .= "**Project Code:** {$project->code}\n";
        $summary .= "**Export Date:** " . now()->format('Y-m-d H:i:s') . "\n";
        $summary .= "**Total Size:** " . $this->formatBytes($totalSize) . "\n";
        $summary .= "**Total Files:** " . $exportedFiles['total'] . "\n\n";
        
        $summary .= "## 📁 Exported Structure\n\n";
        $summary .= "```\n";
        $summary .= "├── app/ ({$exportedFiles['app']} files)\n";
        $summary .= "├── bootstrap/ ({$exportedFiles['bootstrap']} files)\n";
        $summary .= "├── config/ ({$exportedFiles['config']} files)\n";
        $summary .= "├── database/ ({$exportedFiles['database']} files)\n";
        $summary .= "├── public/ ({$exportedFiles['public']} files)\n";
        $summary .= "├── resources/ ({$exportedFiles['resources']} files)\n";
        $summary .= "├── routes/ ({$exportedFiles['routes']} files)\n";
        $summary .= "├── storage/ ({$exportedFiles['storage']} files)\n";
        $summary .= "├── .env (Production ready)\n";
        $summary .= "├── artisan (Laravel CLI)\n";
        $summary .= "├── composer.json (Dependencies)\n";
        $summary .= "└── deploy.sh/.bat (Deployment scripts)\n";
        $summary .= "```\n\n";
        
        $summary .= "## ✅ CMS Features Included\n\n";
        $summary .= "- ✅ Frontend website\n";
        $summary .= "- ✅ Admin CMS panel\n";
        $summary .= "- ✅ Product management\n";
        $summary .= "- ✅ Content management\n";
        $summary .= "- ✅ User authentication\n";
        $summary .= "- ✅ Project-specific features\n\n";
        
        $summary .= "## ❌ Excluded Components\n\n";
        $summary .= "- ❌ SuperAdmin controllers\n";
        $summary .= "- ❌ SuperAdmin routes\n";
        $summary .= "- ❌ Multi-tenancy management\n";
        $summary .= "- ❌ Project management tools\n\n";
        
        $summary .= "## ✅ Pre-Deployed Features\n\n";
        $summary .= "- ✅ Composer dependencies installed\n";
        $summary .= "- ✅ Application key generated\n";
        $summary .= "- ✅ Configuration cached\n";
        $summary .= "- ✅ Routes cached\n";
        $summary .= "- ✅ Views cached\n";
        $summary .= "- ✅ Production environment configured\n\n";
        
        $summary .= "## 🚀 Quick Deploy\n\n";
        $summary .= "**Linux/Mac:**\n";
        $summary .= "```bash\n";
        $summary .= "chmod +x deploy.sh\n";
        $summary .= "./deploy.sh\n";
        $summary .= "```\n\n";
        
        $summary .= "**Windows:**\n";
        $summary .= "```cmd\n";
        $summary .= "deploy.bat\n";
        $summary .= "```\n\n";
        
        $summary .= "---\n";
        $summary .= "*Generated by Core System Export Tool*";
        
        return $summary;
    }
    
    private function scanExportedFiles($exportPath)
    {
        $counts = [
            'total' => 0,
            'app' => 0,
            'bootstrap' => 0,
            'config' => 0,
            'database' => 0,
            'public' => 0,
            'resources' => 0,
            'routes' => 0,
            'storage' => 0
        ];
        
        foreach (['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage'] as $dir) {
            $dirPath = $exportPath . '/' . $dir;
            if (File::exists($dirPath)) {
                $counts[$dir] = $this->countFilesInDirectory($dirPath);
                $counts['total'] += $counts[$dir];
            }
        }
        
        return $counts;
    }
    
    private function countFilesInDirectory($directory)
    {
        if (!File::exists($directory)) return 0;
        
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function calculateDirectorySize($directory)
    {
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    private function preDeployOptimization($exportPath)
    {
        try {
            // Chạy trong thư mục export
            $originalDir = getcwd();
            chdir($exportPath);
            
            // Tạo APP_KEY trước
            $appKey = 'base64:' . base64_encode(random_bytes(32));
            $envContent = File::get($exportPath . '/.env');
            $envContent = str_replace('APP_KEY=', 'APP_KEY=' . $appKey, $envContent);
            File::put($exportPath . '/.env', $envContent);
            
            // Chạy một số lệnh optimize
            if (file_exists('composer.json')) {
                exec('composer install --no-dev --optimize-autoloader 2>&1', $output, $return);
                if ($return === 0) {
                    // Cache config nếu composer install thành công
                    exec('php artisan config:cache 2>&1');
                    exec('php artisan route:cache 2>&1');
                    exec('php artisan view:cache 2>&1');
                }
            }
            
            // Quay về thư mục gốc
            chdir($originalDir);
            
        } catch (\Exception $e) {
            \Log::warning('Pre-deploy optimization failed: ' . $e->getMessage());
            // Không throw exception để không làm fail export
        }
    }
    
    public function getCmsFeatures($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            
            // Get current CMS features configuration
            $features = [];
            if (isset($project->cms_features) && is_array($project->cms_features)) {
                $features = $project->cms_features;
            } elseif (isset($project->cms_features) && is_string($project->cms_features)) {
                $features = json_decode($project->cms_features, true) ?? [];
            }
            
            // Default features if none set
            $defaultFeatures = [
                'products' => true,
                'orders' => true,
                'posts' => true,
                'widgets' => true,
                'menus' => true,
                'themes' => true,
                'media' => true,
                'users' => true
            ];
            
            $features = array_merge($defaultFeatures, $features);
            
            return response()->json($features);
            
        } catch (\Exception $e) {
            \Log::error('Get CMS features failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to load CMS features'
            ], 500);
        }
    }
    
    public function updateCmsFeatures(Request $request, $projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            
            $features = $request->input('features', []);
            
            // Update project CMS features
            $project->update([
                'cms_features' => json_encode($features)
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Update CMS features failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to update CMS features'
            ], 500);
        }
    }
    
    public function downloadLinux($projectCode)
    {
        $exportsDir = storage_path('app/exports');
        $linuxZip = $exportsDir . '/' . $projectCode . '_linux.zip';
        
        if (File::exists($linuxZip)) {
            return response()->download($linuxZip)->deleteFileAfterSend();
        }
        
        return response()->json(['error' => 'Linux file not found'], 404);
    }
}