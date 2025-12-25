<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GrantWidgetPermissionCommand extends Command
{
    protected $signature = 'widget:grant-permission {user_id? : User ID to grant permission} {--email= : User email to grant permission}';
    protected $description = 'Grant widget management permission to a user';

    public function handle(): int
    {
        $userId = $this->argument('user_id');
        $email = $this->option('email');
        
        if (!$userId && !$email) {
            $this->error('Please provide either user_id or --email option');
            return 1;
        }
        
        try {
            if ($userId) {
                $user = User::find($userId);
            } else {
                $user = User::where('email', $email)->first();
            }
            
            if (!$user) {
                $this->error('User not found');
                return 1;
            }
            
            // Update user to have admin level
            $user->update([
                'level' => 'admin',
                'role' => 'admin'
            ]);
            
            $this->info("Successfully granted widget management permission to user: {$user->email}");
            $this->info("User ID: {$user->id}");
            $this->info("Level: admin");
            $this->info("Role: admin");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error granting permission: ' . $e->getMessage());
            return 1;
        }
    }
}