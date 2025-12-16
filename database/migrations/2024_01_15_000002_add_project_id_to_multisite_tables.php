<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'product_categories',
            'brands', 
            'orders',
            'menus',
            'widgets',
            'settings',
            'posts',
            'pages'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                    if (!Schema::hasColumn($table, 'project_id')) {
                        $table_blueprint->unsignedBigInteger('project_id')->nullable()->after('id');
                        $table_blueprint->index('project_id');
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'product_categories',
            'brands',
            'orders', 
            'menus',
            'widgets',
            'settings',
            'posts',
            'pages'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                    if (Schema::hasColumn($table, 'project_id')) {
                        $table_blueprint->dropIndex(['project_id']);
                        $table_blueprint->dropColumn('project_id');
                    }
                });
            }
        }
    }
};