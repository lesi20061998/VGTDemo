<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('permissions');
            $table->timestamps();
        });

        // 2. User Roles
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'role_id']);
        });

        // 3. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create, update, delete, login, etc.
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
        });

        // 4. Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, json, boolean, integer
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['group', 'key']);
        });

        // 5. Menu System
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location'); // header, footer, sidebar
            $table->json('items'); // nested menu structure
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['location', 'is_active']);
        });

        // 6. Widgets
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // text, image, menu, custom
            $table->string('area'); // header, footer, sidebar
            $table->json('settings');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['area', 'is_active', 'sort_order']);
        });

        // 7. System Backups
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // database, files, full
            $table->string('path');
            $table->string('disk')->default('local');
            $table->unsignedBigInteger('size');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['type', 'created_at']);
        });

        // 8. System Logs
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug']);
            $table->string('channel')->default('application');
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
            
            $table->index(['level', 'created_at']);
            $table->index(['channel', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('system_backups');
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
};