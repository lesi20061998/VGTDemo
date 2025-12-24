<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

class ShowDatabaseInstructions extends Command
{
    protected $signature = 'project:database-instructions {projectCode?}';
    protected $description = 'Show manual database creation instructions for Hostinger';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        
        if ($projectCode) {
            $this->showInstructionsForProject($projectCode);
        } else {
            $this->showGeneralInstructions();
        }
    }
    
    private function showInstructionsForProject($projectCode)
    {
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("❌ Project '{$projectCode}' not found!");
            return;
        }
        
        $dbName = $this->getProjectDatabaseName($project);
        $username = env('DB_USERNAME', 'u712054581_VGTApp');
        
        $this->info("🎯 Hướng dẫn tạo database cho project: {$project->name}");
        $this->info("📋 Project Code: {$projectCode}");
        $this->info("💾 Database Name: {$dbName}");
        $this->info("");
        
        $this->info("📝 BƯỚC 1: Đăng nhập Hostinger hPanel");
        $this->info("   1. Truy cập: https://hpanel.hostinger.com");
        $this->info("   2. Đăng nhập tài khoản Hostinger");
        $this->info("");
        
        $this->info("📝 BƯỚC 2: Tạo Database");
        $this->info("   1. Vào: Databases → MySQL Databases");
        $this->info("   2. Tạo database mới với tên: {$dbName}");
        $this->info("   3. Character Set: utf8mb4");
        $this->info("   4. Collation: utf8mb4_unicode_ci");
        $this->info("");
        
        $this->info("📝 BƯỚC 3: Gán quyền User");
        $this->info("   1. Trong section 'Add User to Database'");
        $this->info("   2. User: {$username}");
        $this->info("   3. Database: {$dbName}");
        $this->info("   4. Privileges: ALL PRIVILEGES");
        $this->info("   5. Click 'Add'");
        $this->info("");
        
        $this->info("📝 BƯỚC 4: Kiểm tra kết nối");
        $this->info("   Chạy command: php artisan project:test-database {$projectCode}");
        $this->info("");
        
        $this->info("📝 BƯỚC 5: Tạo Website");
        $this->info("   Sau khi database đã sẵn sàng, click nút 'Tạo Website' trong SuperAdmin");
        $this->info("");
        
        $this->warn("⚠️  LƯU Ý:");
        $this->warn("   - Database name phải chính xác: {$dbName}");
        $this->warn("   - User phải có ALL PRIVILEGES");
        $this->warn("   - Kiểm tra kết nối trước khi tạo website");
    }
    
    private function showGeneralInstructions()
    {
        $this->info("🎯 Hướng dẫn tạo database cho tất cả projects");
        $this->info("");
        
        $projects = Project::where('status', 'assigned')->get();
        
        if ($projects->isEmpty()) {
            $this->info("ℹ️  Không có project nào cần tạo database");
            return;
        }
        
        $this->info("📋 Danh sách projects cần tạo database:");
        $this->info("");
        
        foreach ($projects as $project) {
            $dbName = $this->getProjectDatabaseName($project);
            $this->info("  🔸 {$project->code} → {$dbName}");
        }
        
        $this->info("");
        $this->info("📝 Để xem hướng dẫn chi tiết cho từng project:");
        $this->info("   php artisan project:database-instructions {project_code}");
        $this->info("");
        $this->info("📝 Để kiểm tra kết nối database:");
        $this->info("   php artisan project:test-database {project_code}");
    }
    
    private function getProjectDatabaseName($project)
    {
        $code = $project->code;
        
        if (empty($code)) {
            $code = 'project_'.$project->id;
        }
        
        // HOSTINGER: Add user prefix for production
        if (app()->environment('production')) {
            $username = env('DB_USERNAME', '');
            if (preg_match('/^(u\d+)_/', $username, $matches)) {
                $userPrefix = $matches[1];
                return $userPrefix . '_' . strtolower($code);
            }
        }
        
        return 'project_'.strtolower($code);
    }
}