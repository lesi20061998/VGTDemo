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
        // Create for both mysql and project connections
        $connections = ['mysql', 'project'];
        
        foreach ($connections as $connection) {
            try {
                if (Schema::connection($connection)->hasTable('brand_product')) {
                    continue;
                }
                
                Schema::connection($connection)->create('brand_product', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('product_id');
                    $table->unsignedBigInteger('brand_id');
                    $table->timestamps();
                    
                    $table->unique(['product_id', 'brand_id']);
                    
                    // Add indexes
                    $table->index('product_id');
                    $table->index('brand_id');
                });
                
            } catch (\Exception $e) {
                \Log::warning("Migration brand_product for {$connection}: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('brand_product');
        Schema::connection('project')->dropIfExists('brand_product');
    }
};
