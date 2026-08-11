<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CreateWebsiteCommand extends Command
{
    protected $signature = 'website:create {tenant_code} {--export-path=}';

    protected $description = 'Tạo website mới với database riêng và export source';

    public function handle()
    {
        $tenantCode = $this->argument('tenant_code');
        $exportPath = $this->option('export-path') ?: "c:\\xampp\\htdocs\\{$tenantCode}";

        $tenant = Tenant::where('code', $tenantCode)->first();
        if (! $tenant) {
            $this->error("Tenant {$tenantCode} không tồn tại!");

            return 1;
        }

        $this->info("🚀 Bắt đầu tạo website: {$tenant->name}");

        // 1. Tạo database mới
        $this->createDatabase($tenant);

        // 2. Export source code
        $this->exportSource($tenant, $exportPath);

        // 3. Cấu hình database mới
        $this->setupNewDatabase($tenant, $exportPath);

        // 4. Copy dữ liệu CMS
        $this->copyTenantData($tenant);

        // 5. Tạo router bridge
        $this->createRouterBridge($tenant);

        $this->info("✅ Website {$tenant->name} đã được tạo thành công!");
        $this->info("📁 Source: {$exportPath}");
        $this->info("🌐 URL: http://{$tenant->domain}");

        return 0;
    }

    private function createDatabase($tenant)
    {
        $dbName = $tenant->code.'_db';

        $this->info("📊 Tạo database: {$dbName}");

        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Cập nhật tenant
        $tenant->update(['database_name' => $dbName]);
    }

    private function exportSource($tenant, $exportPath)
    {
        $this->info('📦 Export source code...');

        if (File::exists($exportPath)) {
            File::deleteDirectory($exportPath);
        }

        File::makeDirectory($exportPath, 0755, true);

        // Copy core files
        $coreFiles = [
            'app', 'bootstrap', 'config', 'database', 'public',
            'resources', 'routes', 'storage', 'vendor',
            'artisan', 'composer.json', 'composer.lock', '.env.example',
        ];

        foreach ($coreFiles as $file) {
            $source = base_path($file);
            $dest = $exportPath.DIRECTORY_SEPARATOR.$file;

            if (File::isDirectory($source)) {
                File::copyDirectory($source, $dest);
            } else {
                File::copy($source, $dest);
            }
        }

        // Tạo .env riêng
        $this->createEnvFile($tenant, $exportPath);
    }

    private function createEnvFile($tenant, $exportPath)
    {
        $envContent = "APP_NAME=\"{$tenant->name}\"
APP_ENV=production
APP_KEY=".config('app.key')."
APP_DEBUG=false
APP_URL=http://{$tenant->domain}

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={$tenant->database_name}
DB_USERNAME=root
DB_PASSWORD=

TENANT_ID={$tenant->id}
TENANT_CODE={$tenant->code}
";

        File::put($exportPath.DIRECTORY_SEPARATOR.'.env', $envContent);
    }

    private function setupNewDatabase($tenant, $exportPath)
    {
        $this->info('⚙️ Thiết lập database mới...');

        $mainDb = config('database.connections.mysql.database');
        $dbName = $tenant->database_name;

        DB::statement("USE `{$dbName}`");

        $tables = [
            'users', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
            'products_enhanced', 'product_categories', 'brands', 'product_attributes', 'product_attribute_values',
            'product_attribute_value_mappings', 'product_variations', 'menus', 'menu_items',
            'orders', 'order_items', 'banners', 'contact_forms', 'visitor_logs', 'media', 'migrations',
        ];

        foreach ($tables as $tableName) {
            try {
                $result = DB::select("SHOW CREATE TABLE `{$mainDb}`.`{$tableName}`");
                if (! empty($result)) {
                    $sql = $result[0]->{'Create Table'};
                    $sql = preg_replace('/,\s*CONSTRAINT\s+`[^`]+_tenant_id_foreign`[^,]+/', '', $sql);
                    $sql = preg_replace('/,\s*CONSTRAINT\s+`[^`]+`\s+FOREIGN KEY[^,)]+/', '', $sql);
                    $sql = str_replace("CREATE TABLE `{$tableName}`", "CREATE TABLE IF NOT EXISTS `{$tableName}`", $sql);
                    DB::statement($sql);
                }
            } catch (\Exception $e) {
                $this->warn("Skip {$tableName}: ".$e->getMessage());
            }
        }

        DB::statement("USE `{$mainDb}`");
    }

    private function copyTenantData($tenant)
    {
        $this->info('📋 Copy dữ liệu tenant...');

        $tables = [
            'users', 'posts', 'products_enhanced', 'product_categories',
            'brands', 'settings', 'tags', 'page_sections',
        ];

        $oldDb = config('database.default');

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $data = DB::table($table)->where('tenant_id', $tenant->id)->get();

                if ($data->count() > 0) {
                    // Switch to new database
                    config(['database.connections.mysql.database' => $tenant->database_name]);
                    DB::purge('mysql');

                    foreach ($data as $row) {
                        DB::table($table)->insert((array) $row);
                    }

                    // Switch back
                    config(['database.connections.mysql.database' => 'agency_cms']);
                    DB::purge('mysql');
                }
            }
        }
    }

    private function createRouterBridge($tenant)
    {
        $bridgeContent = "<?php
// Router Bridge for {$tenant->name}
// Allows multi-tenancy dashboard to control this website

if (isset(\$_GET['cms_action']) && \$_GET['cms_token'] === '{$tenant->code}_token') {
    switch (\$_GET['cms_action']) {
        case 'status':
            echo json_encode(['status' => 'active', 'tenant' => '{$tenant->code}']);
            break;
        case 'update':
            // Handle updates from CMS
            break;
    }
    exit;
}
";

        File::put(base_path("../../../{$tenant->code}/public/cms_bridge.php"), $bridgeContent);
    }
}
