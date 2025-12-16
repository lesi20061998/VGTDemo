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
        // Thêm cột department vào employees
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->default('dev')->after('position')->comment('dev, account, admin');
        });

        // Bảng projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('client_name');
            $table->date('start_date');
            $table->date('deadline');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->string('contract_file')->nullable();
            $table->text('technical_requirements')->nullable();
            $table->text('features')->nullable();
            $table->string('environment')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamps();
        });

        // Bảng project_members (Dev được gán vào dự án)
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'dev'])->default('dev');
            $table->timestamps();
            $table->unique(['project_id', 'employee_id']);
        });

        // Bảng tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }
};
