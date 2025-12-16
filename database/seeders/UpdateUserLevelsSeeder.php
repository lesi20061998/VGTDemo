<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserLevelsSeeder extends Seeder
{
    public function run(): void
    {
        // Update existing users with default level
        User::whereNull('level')->update(['level' => 2]);
        
        // Set SuperAdmin level
        User::where('email', 'superadmin@example.com')->update(['level' => 0]);
        
        // Set Administrator level
        User::where('email', 'admin@example.com')->update(['level' => 1]);
        
        $this->command->info('User levels updated successfully!');
    }
}
