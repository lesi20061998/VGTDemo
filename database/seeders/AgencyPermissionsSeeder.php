<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AgencyPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Briefs
            [
                'name' => 'manage-briefs',
                'display_name' => 'Quản lý Brief',
                'description' => 'Xem, tạo, sửa, xóa các Brief',
                'group' => 'Agency',
            ],
            [
                'name' => 'approve-briefs',
                'display_name' => 'Duyệt Brief',
                'description' => 'Duyệt Brief để chuyển thành Project',
                'group' => 'Agency',
            ],
            // Tasks
            [
                'name' => 'manage-tasks',
                'display_name' => 'Quản lý Tasks',
                'description' => 'Tạo, sửa, phân công, xóa Task',
                'group' => 'Agency',
            ],
            [
                'name' => 'update-tasks-progress',
                'display_name' => 'Cập nhật Tiến độ Task',
                'description' => 'Đổi trạng thái Task sang In Progress, nộp kết quả Review',
                'group' => 'Agency',
            ],
            [
                'name' => 'review-tasks',
                'display_name' => 'Nghiệm thu Task',
                'description' => 'Đánh giá kết quả Task thành Done hoặc Rework',
                'group' => 'Agency',
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                $perm
            );
        }

        // Fetch roles
        $accountRole = Role::where('name', 'account')->first();
        $devRole = Role::where('name', 'dev')->first();

        // Assign to Account
        if ($accountRole) {
            $accountPerms = Permission::whereIn('name', [
                'manage-briefs',
                'approve-briefs',
                'manage-tasks',
                'review-tasks',
            ])->pluck('id');
            // Add new permissions while keeping existing ones
            $accountRole->permissions()->syncWithoutDetaching($accountPerms);
        }

        // Assign to Dev
        if ($devRole) {
            $devPerms = Permission::whereIn('name', [
                'update-tasks-progress',
            ])->pluck('id');
            $devRole->permissions()->syncWithoutDetaching($devPerms);
        }
    }
}
