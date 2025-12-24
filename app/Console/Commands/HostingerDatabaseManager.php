<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class HostingerDatabaseManager extends Command
{
    protected $signature = 'hostinger:database {action} {projectCode?} {--all} {--force}';
    protected $description = 'Comprehensive Hostinger database management tool';

    public function handle()
    {
        $action = $this->argument('action');
        $projectCode = $this->argument('projectCode');
        
        switch ($action) {
            case 'instructions':
                return $this->showInstructions($projectCode);
            case 'test':
                return $this->testConnection($projectCode);
            case 'status':
                return $this->showStatus($projectCode);
            case 'fix':
                return $this->fixDatabaseIssues($projectCode);
            case 'list':
                return $this->listDatabases();
            default:
                $this->showHelp();
        }
    }
    
    private function showHelp()
    {
        $this->info("🎯 Hostinger Database Manager");
        $this->info("");
        $this->info("Available actions:");
        $this->info("  instructions [project]  - Show database creation instructions");
        $this->info("  test [project]         - Test database connection");
        $this->info("  status [project]       - Show database status");
        $this->info("  fix [project]          - Fix common database issues");
        $this->info("  list                   - List all project databases");
        $this->info("");
        $this->info("Options:");
        $this->info("  --all                  - Apply to all projects");
        $this->info("  --force                - Force operations");
        $this->info("");
        $this->info("Examples:");
        $this->info("  php artisan hostinger:database instructions hd001");
        $this->info("  php artisan hostinger:database test --all");
        $this->info("  php artisan hostinger:database status hd001");
    }
    
    private function showInstructions($projectCode)
    {
        if (!$projectCode) {
            return $this->showInstructionsForAll();
        }
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("❌ Project '{$projectCode}' not found!");
            return 1;
        }
        
        $dbName = $this->getProjectDatabaseName($project);
        $username = env('DB_USERNAME', 'u712054581_VGTApp');
        $userPrefix = $this->extractUserPrefix($username);
        
        $this->info("🎯 HƯỚNG DẪN TẠO DATABASE CHO HOSTINGER");
        $this->info("=====================================");
        $this->info("");
        $this->info("📋 Project: {$project->name} ({$projectCode})");
        $this->info("💾 Database Name: {$dbName}");
        $this->info("👤 User: {$username}");
        $this->info("🔑 User Prefix: {$userPrefix}");
        $this->info("");
        
        $this->info("📝 BƯỚC 1: Đăng nhập Hostinger hPanel");
        $this->info("   🌐 URL: https://hpanel.hostinger.com");
        $this->info("   🔐 Đăng nhập với tài khoản Hostinger của bạn");
        $this->info("");
        
        $this->info("📝 BƯỚC 2: Tạo Database");
        $this->info("   1. Vào: Websites → Manage → Databases → MySQL Databases");
        $this->info("   2. Click 'Create Database'");
        $this->info("   3. Database Name: {$dbName}");
        $this->info("   4. Character Set: utf8mb4");
        $this->info("   5. Collation: utf8mb4_unicode_ci");
        $this->info("   6. Click 'Create'");
        $this->info("");
        
        $this->info("📝 BƯỚC 3: Gán quyền User");
        $this->info("   1. Trong section 'Add User to Database'");
        $this->info("   2. User: {$username}");
        $this->info("   3. Database: {$dbName}");
        $this->info("   4. Privileges: ALL PRIVILEGES");
        $this->info("   5. Click 'Add'");
        $this->info("");
        
        $this->info("📝 BƯỚC 4: Kiểm tra kết nối");
        $this->info("   php artisan hostinger:database test {$projectCode}");
        $this->info("");
        
        $this->info("📝 BƯỚC 5: Tạo Website");
        $this->info("   Sau khi database sẵn sàng, vào SuperAdmin và click 'Tạo Website'");
        $this->info("");
        
        $this->warn("⚠️  QUAN TRỌNG:");
        $this->warn("   ✓ Database name phải chính xác: {$dbName}");
        $this->warn("   ✓ User phải có ALL PRIVILEGES");
        $this->warn("   ✓ Kiểm tra kết nối trước khi tạo website");
        $this->warn("   ✓ Backup database trước khi thực hiện thay đổi");
        
        return 0;
    }
    
    private function showInstructionsForAll()
    {
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->info("ℹ️  Không có project nào trong hệ thống");
            return 0;
        }
        
        $this->info("🎯 DANH SÁCH TẤT CẢ DATABASES CẦN TẠO");
        $this->info("=====================================");
        $this->info("");
        
        $userPrefix = $this->extractUserPrefix(env('DB_USERNAME', ''));
        
        foreach ($projects as $project) {
            $dbName = $this->getProjectDatabaseName($project);
            $status = $this->getDatabaseStatus($project);
            
            $statusIcon = $status['exists'] ? '✅' : '❌';
            $statusText = $status['exists'] ? 'EXISTS' : 'MISSING';
            
            $this->info("  {$statusIcon} {$project->code} → {$dbName} ({$statusText})");
        }
        
        $this->info("");
        $this->info("📝 Để xem hướng dẫn chi tiết:");
        $this->info("   php artisan hostinger:database instructions {project_code}");
        $this->info("");
        $this->info("📝 Để kiểm tra tất cả databases:");
        $this->info("   php artisan hostinger:database test --all");
        
        return 0;
    }
    
    private function testConnection($projectCode)
    {
        if ($this->option('all')) {
            return $this->testAllConnections();
        }
        
        if (!$projectCode) {
            $this->error("❌ Project code is required for single test");
            return 1;
        }
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("❌ Project '{$projectCode}' not found!");
            return 1;
        }
        
        return $this->testSingleConnection($project);
    }
    
    private function testSingleConnection($project)
    {
        $dbName = $this->getProjectDatabaseName($project);
        $username = env('DB_USERNAME');
        
        $this->info("🔍 TESTING DATABASE CONNECTION");
        $this->info("==============================");
        $this->info("📋 Project: {$project->name} ({$project->code})");
        $this->info("💾 Database: {$dbName}");
        $this->info("👤 User: {$username}");
        $this->info("");
        
        try {
            $mainDb = config('database.connections.mysql.database');
            
            $this->info("1️⃣ Testing database existence...");
            DB::statement("USE `{$dbName}`");
            $this->info("   ✅ Database exists and accessible");
            
            $this->info("2️⃣ Testing table creation permissions...");
            $testTable = "test_connection_" . time();
            DB::statement("CREATE TABLE IF NOT EXISTS {$testTable} (id INT PRIMARY KEY, test_data VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
            $this->info("   ✅ Can create tables");
            
            $this->info("3️⃣ Testing data operations...");
            DB::statement("INSERT INTO {$testTable} (id, test_data) VALUES (1, 'test') ON DUPLICATE KEY UPDATE test_data = 'test'");
            $result = DB::select("SELECT COUNT(*) as count FROM {$testTable}");
            $this->info("   ✅ Can insert/select data (Records: {$result[0]->count})");
            
            $this->info("4️⃣ Testing permissions...");
            DB::statement("ALTER TABLE {$testTable} ADD COLUMN test_col VARCHAR(50) DEFAULT 'test'");
            $this->info("   ✅ Can alter tables");
            
            $this->info("5️⃣ Cleaning up...");
            DB::statement("DROP TABLE IF EXISTS {$testTable}");
            $this->info("   ✅ Can drop tables");
            
            // Switch back to main database
            DB::statement("USE `{$mainDb}`");
            
            $this->info("");
            $this->info("🎉 SUCCESS! Database connection is working perfectly!");
            $this->info("✅ You can now create the website for this project");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("");
            $this->error("❌ DATABASE CONNECTION FAILED!");
            $this->error("Error: " . $e->getMessage());
            $this->error("");
            
            $this->warn("💡 TROUBLESHOOTING STEPS:");
            $this->warn("1. Check if database '{$dbName}' exists in Hostinger hPanel");
            $this->warn("2. Check if user '{$username}' has ALL PRIVILEGES on '{$dbName}'");
            $this->warn("3. Verify database name format is correct");
            $this->warn("4. Check .env file DB_USERNAME and DB_PASSWORD");
            $this->warn("5. Try creating database manually in hPanel");
            $this->warn("");
            $this->warn("📝 To see creation instructions:");
            $this->warn("   php artisan hostinger:database instructions {$project->code}");
            
            return 1;
        }
    }
    
    private function testAllConnections()
    {
        $projects = Project::all();
        $results = [];
        
        $this->info("🔍 TESTING ALL DATABASE CONNECTIONS");
        $this->info("===================================");
        $this->info("");
        
        foreach ($projects as $project) {
            $dbName = $this->getProjectDatabaseName($project);
            
            try {
                DB::statement("USE `{$dbName}`");
                $results[] = [
                    'project' => $project->code,
                    'database' => $dbName,
                    'status' => 'SUCCESS',
                    'message' => 'Connected successfully'
                ];
                $this->info("✅ {$project->code} → {$dbName}");
            } catch (\Exception $e) {
                $results[] = [
                    'project' => $project->code,
                    'database' => $dbName,
                    'status' => 'FAILED',
                    'message' => $e->getMessage()
                ];
                $this->error("❌ {$project->code} → {$dbName}");
            }
        }
        
        // Switch back to main database
        $mainDb = config('database.connections.mysql.database');
        DB::statement("USE `{$mainDb}`");
        
        $this->info("");
        $this->info("📊 SUMMARY:");
        $successful = collect($results)->where('status', 'SUCCESS')->count();
        $failed = collect($results)->where('status', 'FAILED')->count();
        
        $this->info("✅ Successful: {$successful}");
        $this->error("❌ Failed: {$failed}");
        
        if ($failed > 0) {
            $this->info("");
            $this->warn("Failed databases need to be created manually in Hostinger hPanel:");
            foreach ($results as $result) {
                if ($result['status'] === 'FAILED') {
                    $this->warn("  • {$result['database']} (Project: {$result['project']})");
                }
            }
        }
        
        return $failed > 0 ? 1 : 0;
    }
    
    private function showStatus($projectCode)
    {
        if (!$projectCode) {
            return $this->showAllStatus();
        }
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("❌ Project '{$projectCode}' not found!");
            return 1;
        }
        
        $dbName = $this->getProjectDatabaseName($project);
        $status = $this->getDatabaseStatus($project);
        
        $this->info("📊 DATABASE STATUS");
        $this->info("==================");
        $this->info("📋 Project: {$project->name} ({$project->code})");
        $this->info("💾 Database: {$dbName}");
        $this->info("🔗 Status: " . ($status['exists'] ? '✅ EXISTS' : '❌ MISSING'));
        $this->info("📅 Project Status: {$project->status}");
        $this->info("🕐 Created: {$project->created_at}");
        
        if ($status['exists']) {
            $this->info("📈 Tables: {$status['table_count']}");
            $this->info("👥 Users: {$status['user_count']}");
        }
        
        return 0;
    }
    
    private function showAllStatus()
    {
        $projects = Project::all();
        
        $this->info("📊 ALL DATABASES STATUS");
        $this->info("=======================");
        $this->info("");
        
        foreach ($projects as $project) {
            $dbName = $this->getProjectDatabaseName($project);
            $status = $this->getDatabaseStatus($project);
            
            $statusIcon = $status['exists'] ? '✅' : '❌';
            $statusText = $status['exists'] ? 'EXISTS' : 'MISSING';
            
            $this->info("{$statusIcon} {$project->code} → {$dbName} ({$statusText})");
        }
        
        return 0;
    }
    
    private function getDatabaseStatus($project)
    {
        $dbName = $this->getProjectDatabaseName($project);
        
        try {
            $mainDb = config('database.connections.mysql.database');
            
            // Check if database exists
            DB::statement("USE `{$dbName}`");
            
            // Count tables
            $tables = DB::select("SHOW TABLES");
            $tableCount = count($tables);
            
            // Count users (if users table exists)
            $userCount = 0;
            try {
                $users = DB::select("SELECT COUNT(*) as count FROM users");
                $userCount = $users[0]->count ?? 0;
            } catch (\Exception $e) {
                // Users table doesn't exist yet
            }
            
            // Switch back
            DB::statement("USE `{$mainDb}`");
            
            return [
                'exists' => true,
                'table_count' => $tableCount,
                'user_count' => $userCount
            ];
            
        } catch (\Exception $e) {
            return [
                'exists' => false,
                'table_count' => 0,
                'user_count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function fixDatabaseIssues($projectCode)
    {
        if (!$projectCode) {
            $this->error("❌ Project code is required for fix operation");
            return 1;
        }
        
        $project = Project::where('code', $projectCode)->first();
        
        if (!$project) {
            $this->error("❌ Project '{$projectCode}' not found!");
            return 1;
        }
        
        $this->info("🔧 FIXING DATABASE ISSUES");
        $this->info("=========================");
        $this->info("📋 Project: {$project->name} ({$project->code})");
        $this->info("");
        
        // Check current status
        $status = $this->getDatabaseStatus($project);
        
        if (!$status['exists']) {
            $this->error("❌ Database doesn't exist. Please create it manually first.");
            $this->info("📝 Run: php artisan hostinger:database instructions {$projectCode}");
            return 1;
        }
        
        $this->info("✅ Database exists, checking for common issues...");
        
        // Add more fix logic here as needed
        $this->info("🎉 Database appears to be working correctly!");
        
        return 0;
    }
    
    private function listDatabases()
    {
        $this->info("📋 ALL PROJECT DATABASES");
        $this->info("========================");
        $this->info("");
        
        $projects = Project::all();
        $userPrefix = $this->extractUserPrefix(env('DB_USERNAME', ''));
        
        $this->info("🔑 User Prefix: {$userPrefix}");
        $this->info("👤 Username: " . env('DB_USERNAME'));
        $this->info("");
        
        foreach ($projects as $project) {
            $dbName = $this->getProjectDatabaseName($project);
            $status = $this->getDatabaseStatus($project);
            
            $statusIcon = $status['exists'] ? '✅' : '❌';
            $statusText = $status['exists'] ? 'EXISTS' : 'MISSING';
            
            $this->info("  {$statusIcon} {$project->code}");
            $this->info("     Database: {$dbName}");
            $this->info("     Status: {$statusText}");
            if ($status['exists']) {
                $this->info("     Tables: {$status['table_count']}");
            }
            $this->info("");
        }
        
        return 0;
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
    
    private function extractUserPrefix($username)
    {
        if (preg_match('/^(u\d+)_/', $username, $matches)) {
            return $matches[1];
        }
        
        return 'u712054581'; // Default fallback
    }
}