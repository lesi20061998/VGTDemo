<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password 
                            {username=SiVGT : Username của user cần reset (mặc định: SiVGT)}
                            {--password=1 : Mật khẩu mới (mặc định: 1)}';

    protected $description = 'Reset mật khẩu cho user theo username';

    public function handle(): int
    {
        $username = $this->argument('username');
        $newPassword = $this->option('password');

        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->error("Không tìm thấy user với username: {$username}");
            return Command::FAILURE;
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        $this->info("✓ Đã reset mật khẩu cho user: {$username}");
        $this->info("  - Email: {$user->email}");
        $this->info("  - Mật khẩu mới: {$newPassword}");

        return Command::SUCCESS;
    }
}
