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
        Schema::table('projects', function (Blueprint $table) {
            $table->text('project_admin_password_plain')->nullable()->after('project_admin_password');
            $table->timestamp('password_updated_at')->nullable()->after('project_admin_password_plain');
            $table->unsignedBigInteger('password_updated_by')->nullable()->after('password_updated_at');
            
            $table->foreign('password_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['password_updated_by']);
            $table->dropColumn(['project_admin_password_plain', 'password_updated_at', 'password_updated_by']);
        });
    }
};
