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
        // Fix for both mysql and project connections
        $connections = ['mysql', 'project'];
        
        foreach ($connections as $connection) {
            try {
                if (!Schema::connection($connection)->hasTable('product_category_product')) {
                    continue;
                }
                
                // Check if columns exist
                if (!Schema::connection($connection)->hasColumn('product_category_product', 'product_id')) {
                    Schema::connection($connection)->table('product_category_product', function (Blueprint $table) {
                        $table->unsignedBigInteger('product_id')->after('id');
                    });
                }
                
                if (!Schema::connection($connection)->hasColumn('product_category_product', 'product_category_id')) {
                    Schema::connection($connection)->table('product_category_product', function (Blueprint $table) {
                        $table->unsignedBigInteger('product_category_id')->after('product_id');
                    });
                }
                
                // Add indexes if not exist
                $sm = Schema::connection($connection)->getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('product_category_product');
                
                if (!isset($indexes['product_category_product_product_id_product_category_id_unique'])) {
                    Schema::connection($connection)->table('product_category_product', function (Blueprint $table) {
                        $table->unique(['product_id', 'product_category_id'], 'product_category_product_product_id_product_category_id_unique');
                    });
                }
                
            } catch (\Exception $e) {
                \Log::warning("Migration fix for {$connection}: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't remove columns on rollback
    }
};
