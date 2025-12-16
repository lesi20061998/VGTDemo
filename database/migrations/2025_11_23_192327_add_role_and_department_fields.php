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
        // Thêm role phân cấp cho employees nếu chưa có
        if (!Schema::hasColumn('employees', 'position')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->enum('position', ['staff', 'team_lead', 'manager'])->default('staff')->after('is_active');
            });
        }
        
        if (!Schema::hasColumn('employees', 'manager_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');
            });
        }

        // Thêm project_ids cho users
        if (!Schema::hasColumn('users', 'project_ids')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('project_ids')->nullable()->after('level');
            });
        }

        // Thêm website_url cho tickets
        if (!Schema::hasColumn('project_tickets', 'website_url')) {
            Schema::table('project_tickets', function (Blueprint $table) {
                $table->string('website_url')->nullable()->after('project_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['position', 'manager_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('project_ids');
        });

        Schema::table('project_tickets', function (Blueprint $table) {
            $table->dropColumn('website_url');
        });
    }
};
