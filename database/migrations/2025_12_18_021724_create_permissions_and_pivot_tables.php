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
        // Create permissions table if not exists
        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->string('group')->nullable();
                $table->timestamps();
            });
        }

        // Create role_permissions pivot table if not exists
        if (! Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->foreignId('permission_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->unique(['role_id', 'permission_id']);
            });
        }

        // Create user_permissions pivot table if not exists
        if (! Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('permission_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'permission_id']);
            });
        }

        // Add additional fields to roles table if not exists
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('description');
            }
            if (! Schema::hasColumn('roles', 'level')) {
                $table->integer('level')->default(100)->after('is_default');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'is_default')) {
                $table->dropColumn('is_default');
            }
            if (Schema::hasColumn('roles', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
