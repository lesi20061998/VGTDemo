<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access'],
            ['name' => 'editor', 'display_name' => 'Editor', 'description' => 'Content management'],
            ['name' => 'support', 'display_name' => 'Support', 'description' => 'Customer support'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // Assign admin role to first user
        $user = User::first();
        if ($user) {
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole && !$user->roles->contains($adminRole)) {
                $user->roles()->attach($adminRole);
            }
        }
    }
}
