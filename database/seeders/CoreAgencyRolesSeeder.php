<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CoreAgencyRolesSeeder extends Seeder
{
    public function run()
    {
        // 1. Dọn dẹp Role cũ và các quyền không cần thiết
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permissions')->truncate();
        DB::table('user_permissions')->truncate();
        DB::table('user_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Định nghĩa Core Permissions
        $permissions = [
            // Project Management (SuperAdmin scope - dành cho Account/PM và SuperAdmin)
            ['name' => 'manage-contracts', 'display_name' => 'Quản lý Hợp đồng', 'group' => 'Project Management'],
            ['name' => 'manage-briefs', 'display_name' => 'Quản lý Briefs', 'group' => 'Project Management'],
            ['name' => 'approve-briefs', 'display_name' => 'Duyệt Briefs', 'group' => 'Project Management'],
            ['name' => 'manage-projects', 'display_name' => 'Quản lý Dự án', 'group' => 'Project Management'],

            // Task Management (SuperAdmin scope)
            ['name' => 'manage-tasks', 'display_name' => 'Phân công & Quản lý Task', 'group' => 'Task Management'], // Account
            ['name' => 'update-tasks-progress', 'display_name' => 'Cập nhật tiến độ Task', 'group' => 'Task Management'], // Dev
            ['name' => 'review-tasks', 'display_name' => 'Nghiệm thu Task', 'group' => 'Task Management'], // Account

            // CMS Content (CMS scope - dành riêng cho Dev và SuperAdmin)
            ['name' => 'manage-posts', 'display_name' => 'Quản lý Bài viết', 'group' => 'CMS Content'],
            ['name' => 'manage-pages', 'display_name' => 'Quản lý Trang', 'group' => 'CMS Content'],
            ['name' => 'manage-media', 'display_name' => 'Quản lý Media', 'group' => 'CMS Content'],
            ['name' => 'manage-products', 'display_name' => 'Quản lý Sản phẩm & Đơn hàng', 'group' => 'CMS Content'],

            // CMS Technical (CMS scope)
            ['name' => 'manage-theme', 'display_name' => 'Quản lý Giao diện (Theme)', 'group' => 'CMS Technical'],
            ['name' => 'manage-widgets', 'display_name' => 'Quản lý Widgets & Layouts', 'group' => 'CMS Technical'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // 3. Định nghĩa Core Roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Admin', 'description' => 'Quản trị tối cao toàn hệ thống, phân phát tài nguyên']
        );
        $superAdminRole->permissions()->sync(Permission::pluck('id'));

        $accountRole = Role::firstOrCreate(
            ['name' => 'account'],
            ['display_name' => 'Account / PM', 'description' => 'Nhập liệu trung gian, Quản lý Khách hàng, Dự án & Phân Tasks']
        );
        // Account chỉ làm việc ở SuperAdmin
        $accountRole->permissions()->sync(Permission::whereIn('name', [
            'manage-contracts', 'manage-briefs', 'approve-briefs', 'manage-projects', 'manage-tasks', 'review-tasks',
        ])->pluck('id'));

        $devRole = Role::firstOrCreate(
            ['name' => 'dev'],
            ['display_name' => 'Developer / Designer', 'description' => 'Thực thi và xử lý chính mọi thứ trong CMS']
        );
        // Dev cập nhật task và có TOÀN QUYỀN trong CMS
        $devRole->permissions()->sync(Permission::whereIn('name', [
            'update-tasks-progress',
            'manage-posts', 'manage-pages', 'manage-media', 'manage-products', 'manage-theme', 'manage-widgets',
        ])->pluck('id'));

        // 4. Cấp Users mẫu
        $users = [
            [
                'email' => 'superadmin@example.com',
                'name' => 'Super Administrator',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'level' => 0, // Master Level
                'status' => 1,
            ],
            [
                'email' => 'account@example.com',
                'name' => 'Account Manager',
                'username' => 'account_pm',
                'password' => Hash::make('password'),
                'role' => 'account',
                'level' => 1,
                'status' => 1,
            ],
            [
                'email' => 'dev@example.com',
                'name' => 'Senior Developer',
                'username' => 'dev_lead',
                'password' => Hash::make('password'),
                'role' => 'dev',
                'level' => 2,
                'status' => 1,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $roleModel = Role::where('name', $userData['role'])->first();
            if ($roleModel) {
                $user->roles()->sync([$roleModel->id]);
            }
        }
    }
}
