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
        Schema::table('product_attribute_values', function (Blueprint $table) {
            // Add unique constraints to prevent duplicate values within the same attribute
            $table->unique(['product_attribute_id', 'value'], 'unique_attribute_value');
            $table->unique(['product_attribute_id', 'slug'], 'unique_attribute_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->dropUnique('unique_attribute_value');
            $table->dropUnique('unique_attribute_slug');
        });
    }
};
