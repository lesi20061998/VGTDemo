<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tạo bảng tenants trước
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('domain')->unique();
            $table->string('database_name');
            $table->json('settings')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });

        // Thêm foreign key cho users.tenant_id
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                $table->index(['tenant_id']);
            });
        }

        // Danh sách các bảng cần thêm tenant_id (users đã có tenant_id trong migration chính)
        $tables = [
            'posts', 'products_enhanced', 'product_categories', 'brands',
            'orders', 'order_items', 'product_reviews', 'settings', 'tags',
            'page_sections', 'product_attributes', 'product_attribute_values',
            'product_attribute_value_mappings', 'attribute_groups', 'product_variations',
            'form_submissions', 'activity_logs', 'roles', 'projects', 'employees',
            'contracts', 'tasks', 'project_tickets', 'project_settings', 'project_permissions'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                    $table->index(['tenant_id']);
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'users', 'posts', 'products_enhanced', 'product_categories', 'brands',
            'orders', 'order_items', 'product_reviews', 'settings', 'tags',
            'page_sections', 'product_attributes', 'product_attribute_values',
            'product_attribute_value_mappings', 'attribute_groups', 'product_variations',
            'form_submissions', 'activity_logs', 'roles', 'projects', 'employees',
            'contracts', 'tasks', 'project_tickets', 'project_settings', 'project_permissions'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }

        Schema::dropIfExists('tenants');
    }
};