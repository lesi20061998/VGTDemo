<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('widget_templates')) {
            Schema::create('widget_templates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->string('name');
                $table->string('type');
                $table->string('category')->default('general');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('preview_image')->nullable();
                $table->json('config_schema')->nullable();
                $table->json('default_settings')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_premium')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('name');
                $table->string('type');
                $table->string('area');
                $table->json('settings')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('variant')->nullable();
                $table->json('meta_data')->nullable();
                $table->timestamps();

                $table->index(['area', 'is_active', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('widget_templates');
    }
};
