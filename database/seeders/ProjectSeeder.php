<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Contract;
use App\Models\Employee;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo employee mẫu nếu chưa có
        $employee = Employee::firstOrCreate([
            'code' => 'EMP001'
        ], [
            'name' => 'Sample Employee',
            'email' => 'employee@example.com',
            'phone' => '0123456789',
            'position' => 'Developer',
            'is_active' => true
        ]);

        // Tạo contract mẫu nếu chưa có
        $contract = Contract::firstOrCreate([
            'contract_code' => 'CT001'
        ], [
            'full_code' => 'CT001-2025',
            'client_name' => 'Sample Client',
            'service_type' => 'Web Development',
            'requirements' => 'E-commerce system development',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'deadline' => now()->addMonths(6),
            'status' => 'pending',
            'is_active' => true,
            'employee_id' => $employee->id
        ]);

        // Tạo project với code SiVGT
        Project::firstOrCreate([
            'code' => 'SiVGT'
        ], [
            'contract_id' => $contract->id,
            'name' => 'Auto Project SiVGT',
            'client_name' => 'Sample Client',
            'start_date' => now(),
            'deadline' => now()->addMonths(6),
            'status' => 'active',
            'contract_value' => 100000000,
            'technical_requirements' => 'Laravel E-commerce System',
            'features' => 'Product management, Order management, User management',
            'environment' => 'Development',
            'project_admin_username' => 'admin',
            'project_admin_password' => bcrypt('admin123'),
            'created_by' => $employee->id
        ]);
    }
}