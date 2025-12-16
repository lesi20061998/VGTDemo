<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'products_enhanced',
            'product_categories', 
            'brands',
            'orders',
            'menus',
            'widgets',
            'settings',
            'posts'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'project_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedInteger('project_id')->nullable()->after('id');
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
            'orders',
            'menus',
            'widgets',
            'settings',
            'posts'
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