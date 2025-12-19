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
            if (!Schema::hasColumn('products_enhanced', 'language_id')) {
                $table->unsignedBigInteger('language_id')->default(1)->after('product_type');
            }
            // Add index if it doesn't exist
            if (!Schema::hasIndex('products_enhanced', ['language_id', 'status'])) {
                $table->index(['language_id', 'status']); // Add index for better performance
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_enhanced', function (Blueprint $table) {
            $table->dropIndex(['language_id', 'status']);
            $table->dropColumn('language_id');
        });
    }
};
