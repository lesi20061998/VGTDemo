<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip - level đã được thêm trong migration users chính
        if (!Schema::hasColumn('users', 'level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('level')->default(2)->after('role')->comment('0=SuperAdmin, 1=Administrator, 2=User');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
