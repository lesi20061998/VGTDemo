<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tables that need project_id for multi-tenant support
        $tables = [
            'products_enhanced',
            'product_categories', 
            'brands',
            'product_attributes',
            'product_attribute_values',
            'product_attribute_value_mappings',
            'product_reviews',
            'product_variations',
            'orders',
            'order_items',
            'order_status_histories',
            'menus',
            'menu_items',
            'widgets',
            'settings',
            'form_submissions',
            'branches',
            'posts',
            'tags'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'project_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('project_id')->nullable()->after('id');
                    $table->index('project_id');
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'products_enhanced',
            'product_categories', 
            'brands',
            'product_attributes',
            'product_attribute_values',
            'product_attribute_value_mappings',
            'product_reviews',
            'product_variations',
            'orders',
            'order_items',
            'order_status_histories',
            'menus',
            'menu_items',
            'widgets',
            'settings',
            'form_submissions',
            'branches',
            'posts',
            'tags'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'project_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropIndex(['project_id']);
                    $table->dropColumn('project_id');
                });
            }
        }
    }
};