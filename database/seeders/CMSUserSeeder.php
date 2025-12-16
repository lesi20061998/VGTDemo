<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CMSUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'CMS Admin',
            'email' => 'cms@admin.com',
            'password' => Hash::make('123456789'),
            'role' => 'cms',
            'level' => 2,
            'email_verified_at' => now(),
        ]);
    }
}