<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiVGT2ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy hoặc tạo employee
        $employee = DB::table('employees')->where('code', 'ADMIN')->first();
        if (!$employee) {
            $employeeId = DB::table('employees')->insertGetId([
                'code' => 'SIVGT2_ADMIN',
                'name' => 'SiVGT2 Admin',
                'email' => 'admin@sivgt2.com',
                'position' => 'Project Admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $employeeId = $employee->id;
        }

        // Tạo contract đơn giản
        $contractId = DB::table('contracts')->insertGetId([
            'employee_id' => $employeeId,
            'contract_code' => 'CT_SiVGT2',
            'full_code' => 'CT_SiVGT2_2025',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Tạo project SiVGT2
        DB::table('projects')->insert([
            'contract_id' => $contractId,
            'name' => 'SiVGT2 E-commerce Project',
            'code' => 'SiVGT2',
            'client_name' => 'SiVGT2 Client',
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'active',
            'contract_value' => 100000000,
            'technical_requirements' => 'Laravel E-commerce System with Admin Panel',
            'features' => 'Product Management, Order Management, User Management, Content Management',
            'environment' => 'Development',
            'project_admin_username' => 'admin',
            'project_admin_password' => bcrypt('admin123'),
            'admin_id' => $employeeId,
            'created_by' => $employeeId,
            'approved_at' => now(),
            'initialized_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ Project SiVGT2 đã được tạo thành công!\n";
        echo "🔗 Login URL: http://localhost:8000/SiVGT2/login\n";
        echo "⚙️ Admin Panel: http://localhost:8000/SiVGT2/admin\n";
        echo "👤 Username: admin\n";
        echo "🔑 Password: admin123\n";
    }
}