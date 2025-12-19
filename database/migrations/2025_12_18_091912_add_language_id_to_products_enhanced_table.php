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
        Schema::table('products_enhanced', function (Blueprint $table) {
            $table->foreignId('language_id')->default(1)->after('product_type');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');

            // Add index for better performance when querying by language
            $table->index(['language_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_enhanced', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropIndex(['language_id', 'status']);
            $table->dropColumn('language_id');
        });
    }
};
