<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;

class CreateProjectCommand extends Command
{
    protected $signature = 'project:create {code} {--name=} {--client=} {--admin-username=admin} {--admin-password=admin123}';
    protected $description = 'Tạo project mới tự động';

    public function handle()
    {
        $code = $this->argument('code');
        $name = $this->option('name') ?: 'Project ' . $code;
        $client = $this->option('client') ?: 'Client ' . $code;
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
                $employee = Employee::firstOrCreate(
                    ['code' => 'AUTO_ADMIN'],
                    [
                        'name' => 'Auto Admin',
                        'email' => 'auto.admin@system.local',
                        'position' => 'System Admin',
                        'department' => 'admin',
                        'is_active' => true
                    ]
                );

                // Tạo contract
                $contract = Contract::create([
                    'employee_id' => $employee->id,
                    'contract_code' => 'CT_' . $code,
                    'full_code' => 'CT_' . $code . '_' . date('Y'),
                    'client_name' => $client,
                    'service_type' => 'Web Development',
                    'requirements' => 'Project requirements for ' . $name,
                    'start_date' => now(),
                    'end_date' => now()->addMonths(12),
                    'deadline' => now()->addMonths(12),
                    'status' => 'approved',
                    'is_active' => true
                ]);

                // Tạo project
                $project = Project::create([
                    'contract_id' => $contract->id,
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
                    'initialized_at' => now()
                ]);

                $this->info("✅ Project '{$code}' đã được tạo thành công!");
                $this->info("📋 Thông tin truy cập:");
                $this->info("🔗 Login URL: http://localhost:8000/{$code}/login");
                $this->info("⚙️  Admin Panel: http://localhost:8000/{$code}/admin");
                $this->info("👤 Username: {$adminUsername}");
                $this->info("🔑 Password: {$adminPassword}");
            });

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Lỗi khi tạo project: " . $e->getMessage());
            return 1;
        }
    }
}
