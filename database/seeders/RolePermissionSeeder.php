<?php
// MODIFIED: 2025-01-21

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create basic roles using existing table structure
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access', 'permissions' => '[]', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editor', 'display_name' => 'Editor', 'description' => 'Content management', 'permissions' => '[]', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'support', 'display_name' => 'Support', 'description' => 'Customer support', 'permissions' => '[]', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role using user_roles table
        DB::table('user_roles')->insert([
            'user_id' => $admin->id,
            'role_id' => 1, // admin role
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}