<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ExportFullCMS extends Command
{
    protected $signature = 'project:export-full {projectCode}';

    protected $description = 'Export complete CMS with source code and database';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');

        $project = Project::where('code', $projectCode)->first();

        if (! $project) {
            $this->error("Project with code '{$projectCode}' not found!");

            return 1;
        }

        $this->info("🚀 Exporting FULL CMS: {$project->name} ({$projectCode})");
        $this->info('� Thpis includes: Source + Database + Config');

        // Tạo thư mục export
        $exportDir = storage_path("app/full-exports/{$projectCode}");
        $this->createExportDirectory($exportDir);

        // 1. Copy essential Laravel files only
        $this->copyEssentialFiles($exportDir);

        // 2. Export database
        $this->exportDatabase($project, $exportDir);

        // 3. Tạo config và installer
        $this->createInstaller($project, $exportDir);

        // 4. Tạo zip
        $zipPath = $this->createZip($projectCode, $exportDir);

        $this->info('✅ FULL CMS exported successfully!');
        $this->info("📦 Export: {$zipPath}");

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

        // Copy key directories
        $dirs = ['app', 'config', 'database', 'resources', 'routes'];

        foreach ($dirs as $dir) {
            if (File::exists(base_path($dir))) {
                $this->info("  📂 Copying {$dir}...");
                File::copyDirectory(base_path($dir), "{$exportDir}/{$dir}");
            }
        }

        // Copy key files
        $files = ['artisan', 'composer.json'];
        foreach ($files as $file) {
            if (File::exists(base_path($file))) {
                File::copy(base_path($file), "{$exportDir}/{$file}");
            }
        }

        // Create basic structure
        File::makeDirectory("{$exportDir}/storage/logs", 0755, true);
        File::makeDirectory("{$exportDir}/bootstrap/cache", 0755, true);
        File::makeDirectory("{$exportDir}/public", 0755, true);

        $this->info('✅ Essential files copied');
    }

    private function exportDatabase($project, $exportDir)
    {
        $this->info('💾 Exporting database...');

        $dbName = 'project_'.strtolower($project->code);
        $sqlFile = "{$exportDir}/database.sql";

        // Create simple database export instructions
        $instructions = "-- Database Export Instructions
-- 
-- Original Database: {$dbName}
-- Target Database: {$project->code}_cms
--
-- To export:
-- mysqldump -u username -p {$dbName} > database.sql
--
-- To import:
-- mysql -u username -p {$project->code}_cms < database.sql
--
-- Database Info:
-- Host: ".env('DB_HOST').'
-- Username: '.env('DB_USERNAME')."
-- Project ID: {$project->id}
";

        File::put($sqlFile, $instructions);
        $this->info('✅ Database export instructions created');
    }

    private function createInstaller($project, $exportDir)
    {
        $this->info('🔧 Creating installer...');

        // .env file
        $envContent = "APP_NAME=\"{$project->name}\"
APP_ENV=production
APP_KEY=base64:qpm3Itwm80pJSW6AlOoWfz9+I7YVwTHc4vlIp7ChfTs=
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

        File::put("{$exportDir}/.env", $envContent);

        // README
        $readme = "# {$project->name} - Complete CMS

## Quick Start
1. Extract files to web directory
2. Run: composer install
3. Update .env with database credentials
4. Create database: {$project->code}_cms
5. Import: mysql -u user -p {$project->code}_cms < database.sql
6. Access: /admin

## What's Included
- Complete Laravel CMS
- Database export instructions
- Configuration files
- Admin panel at /admin

## Requirements
- PHP 8.1+
- MySQL 5.7+
- Composer

Project: {$project->name} ({$project->code})
Export: ".date('Y-m-d H:i:s').'
';

        File::put("{$exportDir}/README.md", $readme);

        $this->info('✅ Installer created');
    }

    private function createZip($projectCode, $exportDir)
    {
        $this->info('📦 Creating zip...');

        $zipPath = storage_path("app/full-exports/{$projectCode}_full.zip");

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
            File::deleteDirectory($exportDir);

            return $zipPath;
        }

        throw new \Exception('Cannot create zip file');
    }
}
