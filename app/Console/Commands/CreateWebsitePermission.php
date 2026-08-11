<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateWebsitePermission extends Command
{
    protected $signature = 'permission:create-website';

    protected $description = 'Create website creation permission and assign to roles';

    public function handle()
    {
        $this->info('🔧 Creating website creation permission...');

        // Tạo permission
        $permission = Permission::firstOrCreate([
            'name' => 'create-websites',
            'guard_name' => 'web',
        ]);

        $this->info("✅ Permission 'create-websites' created/found");

        // Gán permission cho các role
        $rolesToAssign = [
            'super-admin',
            'admin',
            'manager',
        ];

        foreach ($rolesToAssign as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                if (! $role->hasPermissionTo('create-websites')) {
                    $role->givePermissionTo('create-websites');
                    $this->info("✅ Assigned 'create-websites' to role: {$roleName}");
                } else {
                    $this->info("ℹ️  Role '{$roleName}' already has 'create-websites' permission");
                }
            } else {
                $this->warn("⚠️  Role '{$roleName}' not found");
            }
        }

        // Hiển thị danh sách roles hiện có
        $this->info("\n📋 Available roles:");
        $roles = Role::all();
        foreach ($roles as $role) {
            $hasPermission = $role->hasPermissionTo('create-websites') ? '✅' : '❌';
            $this->info("  {$hasPermission} {$role->name}");
        }

        $this->info("\n🎯 To assign permission to a specific role:");
        $this->info('   php artisan permission:assign create-websites role_name');

        return 0;
    }
}
