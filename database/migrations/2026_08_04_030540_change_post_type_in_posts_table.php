<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE posts MODIFY post_type VARCHAR(50) DEFAULT 'post'");
        } else {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('post_type', 50)->default('post')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting back to enum is risky, but here is the definition
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE posts MODIFY post_type ENUM('post', 'page') DEFAULT 'post'");
        } else {
            Schema::table('posts', function (Blueprint $table) {
                // Not supported easily in SQLite without full rebuild, so we leave it as string
            });
        }
    }
};
