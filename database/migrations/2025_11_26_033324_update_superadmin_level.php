<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cập nhật level = 0 cho tất cả superadmin
        DB::table('users')
            ->where('role', 'superadmin')
            ->update(['level' => 0]);
    }

    public function down(): void
    {
        // Không cần rollback
    }
};
