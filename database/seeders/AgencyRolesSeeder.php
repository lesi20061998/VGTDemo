<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgencyRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create 'account' role
        $accountRole = Role::firstOrCreate(
            ['name' => 'account'],
            ['display_name' => 'Account Executive', 'description' => 'Quản lý khách hàng và dự án']
        );

        // 2. Create 'dev' role
        $devRole = Role::firstOrCreate(
            ['name' => 'dev'],
            ['display_name' => 'Developer', 'description' => 'Lập trình viên thực thi dự án']
        );

        // 3. Create Sample Account User
        $accountUser = User::firstOrCreate(
            ['email' => 'account@example.com'],
            [
                'name' => 'Demo Account',
                'username' => 'demo_account',
                'password' => \Hash::make('password'),
                'role' => 'account',
                'level' => 1, // Same as admin but logic can be differentiated by role
                'status' => 1,
            ]
        );
        $accountUser->roles()->syncWithoutDetaching([$accountRole->id]);

        // 4. Create Sample Dev User
        $devUser = User::firstOrCreate(
            ['email' => 'dev@example.com'],
            [
                'name' => 'Demo Dev',
                'username' => 'demo_dev',
                'password' => \Hash::make('password'),
                'role' => 'dev',
                'level' => 2,
                'status' => 1,
            ]
        );
        $devUser->roles()->syncWithoutDetaching([$devRole->id]);
    }
}
