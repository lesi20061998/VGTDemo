<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseConnection extends Command
{
    protected $signature = 'project:test-database {projectCode}';

    protected $description = 'Test database connection for a project';

    public function handle()
    {
        $projectCode = $this->argument('projectCode');

        $project = Project::where('code', $projectCode)->first();

        if (! $project) {
            $this->error("❌ Project '{$projectCode}' not found!");

            return 1;
        }

        $dbName = $this->getProjectDatabaseName($project);
        $username = env('DB_USERNAME');

        $this->info('🔍 Testing database connection...');
        $this->info("📋 Project: {$project->name} ({$projectCode})");
        $this->info("💾 Database: {$dbName}");
        $this->info("👤 User: {$username}");
        $this->info('');

        try {
            // Test connection
            $mainDb = config('database.connections.mysql.database');

            $this->info('1️⃣ Testing database existence...');
            DB::statement("USE `{$dbName}`");
            $this->info('   ✅ Database exists and accessible');

            $this->info('2️⃣ Testing table creation...');
            DB::statement('CREATE TABLE IF NOT EXISTS test_connection (id INT PRIMARY KEY, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
            $this->info('   ✅ Can create tables');

            $this->info('3️⃣ Testing data operations...');
            DB::statement('INSERT INTO test_connection (id) VALUES (1) ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP');
            $result = DB::select('SELECT COUNT(*) as count FROM test_connection');
            $this->info("   ✅ Can insert/select data (Records: {$result[0]->count})");

            $this->info('4️⃣ Cleaning up...');
            DB::statement('DROP TABLE IF EXISTS test_connection');
            $this->info('   ✅ Can drop tables');

            // Switch back to main database
            DB::statement("USE `{$mainDb}`");

            $this->info('');
            $this->info('🎉 SUCCESS! Database connection is working perfectly!');
            $this->info('✅ You can now create the website for this project');

            return 0;

        } catch (\Exception $e) {
            $this->error('');
            $this->error('❌ DATABASE CONNECTION FAILED!');
            $this->error('Error: '.$e->getMessage());
            $this->error('');

            $this->warn('💡 TROUBLESHOOTING STEPS:');
            $this->warn("1. Check if database '{$dbName}' exists in Hostinger hPanel");
            $this->warn("2. Check if user '{$username}' has ALL PRIVILEGES on '{$dbName}'");
            $this->warn('3. Verify database name format is correct');
            $this->warn('4. Check .env file DB_USERNAME and DB_PASSWORD');
            $this->warn('');
            $this->warn('📝 To see creation instructions:');
            $this->warn("   php artisan project:database-instructions {$projectCode}");

            return 1;
        }
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

                return $userPrefix.'_'.strtolower($code);
            }
        }

        return 'project_'.strtolower($code);
    }
}
