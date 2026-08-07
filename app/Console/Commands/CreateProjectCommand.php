<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateProjectCommand extends Command
{
    protected $signature = 'project:create {code} {--name=} {--client=} {--admin-username=admin} {--admin-password=admin123}';

    protected $description = 'Tạo project mới tự động';

    public function handle()
    {
        $code = $this->argument('code');
        $name = $this->option('name') ?: 'Project '.$code;
        $client = $this->option('client') ?: 'Client '.$code;
        $adminUsername = $this->option('admin-username');
        $adminPassword = $this->option('admin-password');

        // Kiểm tra project đã tồn tại
        if (Project::where('code', $code)->exists()) {
            $this->error("Project với code '{$code}' đã tồn tại!");

            return 1;
        }

        try {
            DB::transaction(function () use ($code, $name, $client, $adminUsername, $adminPassword) {
                // Tạo hoặc lấy employee mặc định
                $employee = User::firstOrCreate(
                    ['username' => 'AUTO_ADMIN'],
                    [
                        'name' => 'Auto Admin',
                        'email' => 'auto.admin@system.local',
                        'role' => 'superadmin',
                        'password' => bcrypt('password'),
                    ]
                );

                $contractId = 0; // Mock contract ID

                // Tạo project
                $project = Project::create([
                    'contract_id' => $contractId,
                    'name' => $name,
                    'code' => $code,
                    'client_name' => $client,
                    'start_date' => now(),
                    'deadline' => now()->addMonths(12),
                    'status' => 'active',
                    'contract_value' => 50000000,
                    'technical_requirements' => 'Laravel CMS/E-commerce System',
                    'features' => 'Content Management, Product Management, Order Management',
                    'environment' => 'Production',
                    'project_admin_username' => $adminUsername,
                    'project_admin_password' => bcrypt($adminPassword),
                    'admin_id' => $employee->id,
                    'created_by' => $employee->id,
                    'approved_at' => now(),
                    'initialized_at' => now(),
                ]);

                $this->info("✅ Project '{$code}' đã được tạo thành công!");
                $this->info('📋 Thông tin truy cập:');
                $this->info("🔗 Login URL: http://localhost:8000/{$code}/login");
                $this->info("⚙️  Admin Panel: http://localhost:8000/{$code}/admin");
                $this->info("👤 Username: {$adminUsername}");
                $this->info("🔑 Password: {$adminPassword}");
            });

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi tạo project: '.$e->getMessage());

            return 1;
        }
    }
}
