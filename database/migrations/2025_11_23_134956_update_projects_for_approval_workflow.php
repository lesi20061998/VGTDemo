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
            $table->dropColumn('status');
        });
        
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('status', ['pending', 'assigned', 'active', 'error', 'completed', 'cancelled'])->default('pending')->after('deadline');
            $table->foreignId('created_by')->nullable()->after('admin_id')->constrained('employees')->onDelete('set null');
            $table->string('subdomain')->nullable()->after('code');
            $table->string('project_admin_username')->nullable()->after('subdomain');
            $table->string('project_admin_password')->nullable()->after('project_admin_username');
            $table->timestamp('approved_at')->nullable()->after('notes');
            $table->timestamp('initialized_at')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'subdomain', 'project_admin_username', 'project_admin_password', 'approved_at', 'initialized_at']);
        });
    }
};
