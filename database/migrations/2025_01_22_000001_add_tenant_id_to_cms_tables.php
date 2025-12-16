<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'brands',
            'product_categories',
            'product_attributes',
            'product_attribute_values',
            'orders',
            'order_items',
            'product_reviews',
            'form_submissions',
            'visitor_logs',
            'tags',
            'fonts'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                    $table->index('tenant_id');
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'brands',
            'product_categories',
            'product_attributes',
            'product_attribute_values',
            'orders',
            'order_items',
            'product_reviews',
            'form_submissions',
            'visitor_logs',
            'tags',
            'fonts'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropIndex(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
