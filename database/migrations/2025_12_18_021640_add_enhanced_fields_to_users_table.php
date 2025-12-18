<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->string('phone')->nullable()->after('avatar');
            $table->text('address')->nullable()->after('phone');
            $table->boolean('status')->default(true)->after('address');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->json('preferences')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone',
                'address',
                'status',
                'last_login_at',
                'preferences',
            ]);
        });
    }
};
