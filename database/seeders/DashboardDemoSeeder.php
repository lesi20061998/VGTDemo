<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DashboardDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to act as staff
        $staffIds = User::pluck('id')->toArray();
        if (empty($staffIds)) {
            // Create a default user if none exists
            $user = User::create([
                'name' => 'Demo Admin',
                'email' => 'admin_demo@example.com',
                'password' => bcrypt('password'),
            ]);
            $staffIds = [$user->id];
        }

        $services = [
            'Thiết kế website',
            'Thiết kế ấn phẩm',
            'Thiết kế nhận diện thương hiệu',
            'Sản xuất nội dung mạng xã hội',
            'Quản trị hệ thống',
        ];

        // 2. Tạo Projects (Dự án) metadata
        $projectData = [
            ['name' => 'Dự án Landing Page', 'deadline' => now()->addDays(1)], // Urgent
            ['name' => 'Dự án App Mobile', 'deadline' => now()->addDays(2)], // Urgent
            ['name' => 'Dự án Cổng TTĐT', 'deadline' => now()->subDays(1)], // Overdue
            ['name' => 'Dự án Thương mại điện tử', 'deadline' => now()->addDays(10)], // Safe
            ['name' => 'Dự án Quản lý nhân sự', 'deadline' => now()->addDays(5)], // Safe
        ];

        foreach ($projectData as $index => $data) {
            $serviceType = $services[$index % count($services)];

            // 1. Tạo Contract tương ứng
            $contract = Contract::create([
                'title' => "Hợp đồng {$serviceType} #".($index + 1),
                'client_name' => 'Khách hàng '.($index + 1),
                'service_type' => $serviceType,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->addMonths(6),
                'contract_value' => rand(10, 50) * 1000000, // 10M to 50M
                'status' => 'active',
                'created_at' => now(), // created this month
            ]);

            $adminId = $staffIds[array_rand($staffIds)];

            $project = Project::create([
                'name' => $data['name'],
                'code' => strtoupper(Str::random(6)),
                'contract_id' => $contract->id,
                'client_name' => $contract->client_name,
                'status' => 'active',
                'start_date' => now()->subDays(5),
                'deadline' => $data['deadline'],
                'admin_id' => $adminId, // Person in charge
            ]);

            // 3. Tạo Tasks cho từng Project để hiển thị tiến độ
            $totalTasks = rand(3, 7);
            for ($t = 1; $t <= $totalTasks; $t++) {
                // Random status
                $statusChoices = ['pending', 'in_progress', 'completed'];
                // Ensure at least some tasks are completed so progress is > 0
                $status = ($t <= ceil($totalTasks / 2)) ? 'completed' : $statusChoices[array_rand($statusChoices)];
                $devId = $staffIds[array_rand($staffIds)];

                Task::create([
                    'project_id' => $project->id,
                    'title' => "Công việc {$t} của ".$project->name,
                    'description' => "Mô tả công việc {$t}",
                    'status' => $status,
                    'deadline' => $project->deadline->subDays(rand(1, 3)),
                    'dev_id' => $devId, // Person in charge
                ]);
            }
        }
    }
}
