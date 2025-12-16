<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot table: Product + Attribute Values (many-to-many)
        Schema::create('product_attribute_value_mappings', function (Blueprint $table) {
            $table->id();

            // Use explicit unsignedBigInteger columns and short FK names to avoid MySQL identifier length limits
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_attribute_id');
            $table->unsignedBigInteger('product_attribute_value_id');

            $table->timestamps();

            // Foreign keys with short, explicit names
            $table->foreign('product_id', 'fk_pavm_prod')
                ->references('id')->on('products_enhanced')
                ->onDelete('cascade');

            $table->foreign('product_attribute_id', 'fk_pavm_attr')
                ->references('id')->on('product_attributes')
                ->onDelete('cascade');

            $table->foreign('product_attribute_value_id', 'fk_pavm_val')
                ->references('id')->on('product_attribute_values')
                ->onDelete('cascade');

            // Unique constraint để tránh duplicate
            $table->unique(['product_id', 'product_attribute_id', 'product_attribute_value_id'], 'unique_product_attribute_value');

            // Index để query nhanh
            $table->index(['product_id', 'product_attribute_id'], 'idx_pavm_prod_attr');
            $table->index(['product_attribute_id', 'product_attribute_value_id'], 'idx_pavm_attr_val');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_value_mappings');
    }
};
