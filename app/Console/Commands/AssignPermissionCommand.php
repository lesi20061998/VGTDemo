<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignPermissionCommand extends Command
{
    protected $signature = 'permission:assign {permission} {target} {--type=role}';
    protected $description = 'Assign permission to role or user';

    public function handle()
    {
        $permission = $this->argument('permission');
        $target = $this->argument('target');
        $type = $this->option('type'); // 'role' or 'user'
        
        // Kiểm tra permission tồn tại
        if (!Permission::where('name', $permission)->exists()) {
            $this->error("❌ Permission '{$permission}' does not exist!");
            return 1;
        }
        
        if ($type === 'role') {
            return $this->assignToRole($permission, $target);
        } elseif ($type === 'user') {
            return $this->assignToUser($permission, $target);
        } else {
            $this->error("❌ Invalid type. Use --type=role or --type=user");
            return 1;
        }
    }
    
    private function assignToRole($permission, $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("❌ Role '{$roleName}' not found!");
            $this->info("📋 Available roles:");
            Role::all()->each(fn($r) => $this->info("  - {$r->name}"));
            return 1;
        }
        
        if ($role->hasPermissionTo($permission)) {
            $this->info("ℹ️  Role '{$roleName}' already has permission '{$permission}'");
            return 0;
        }
        
        $role->givePermissionTo($permission);
        $this->info("✅ Assigned permission '{$permission}' to role '{$roleName}'");
        
        return 0;
    }
    
    private function assignToUser($permission, $userIdentifier)
    {
        // Tìm user theo email hoặc ID
        $user = User::where('email', $userIdentifier)
                   ->orWhere('id', $userIdentifier)
                   ->first();
        
        if (!$user) {
            $this->error("❌ User '{$userIdentifier}' not found!");
            return 1;
        }
        
        if ($user->hasPermissionTo($permission)) {
            $this->info("ℹ️  User '{$user->email}' already has permission '{$permission}'");
            return 0;
        }
        
        $user->givePermissionTo($permission);
        $this->info("✅ Assigned permission '{$permission}' to user '{$user->email}'");
        
        return 0;
    }
}