<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('cms')->comment('admin/cms/employee');
            $table->tinyInteger('level')->default(2)->comment('0=SuperAdmin, 1=Administrator, 2=User');
            $table->json('project_ids')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->rememberToken();
            // Two-Factor Authentication Laravel Fortify
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
