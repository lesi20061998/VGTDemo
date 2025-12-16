<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            return;
        }
        
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('subdomain')->nullable();
            $table->string('project_admin_username')->nullable();
            $table->string('project_admin_password')->nullable();
            $table->string('client_name');
            $table->date('start_date');
            $table->date('deadline');
            $table->enum('status', ['pending', 'assigned', 'active', 'error', 'completed', 'cancelled'])->default('pending');
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->string('contract_file')->nullable();
            $table->text('technical_requirements')->nullable();
            $table->text('features')->nullable();
            $table->string('environment')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('initialized_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};