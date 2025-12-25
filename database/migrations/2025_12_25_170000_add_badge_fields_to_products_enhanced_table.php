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
        Schema::connection('project')->table('products_enhanced', function (Blueprint $table) {
            if (!Schema::connection('project')->hasColumn('products_enhanced', 'is_favorite')) {
                $table->boolean('is_favorite')->default(false)->after('is_featured');
            }
            if (!Schema::connection('project')->hasColumn('products_enhanced', 'is_bestseller')) {
                $table->boolean('is_bestseller')->default(false)->after('is_favorite');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('project')->table('products_enhanced', function (Blueprint $table) {
            $table->dropColumn(['is_favorite', 'is_bestseller']);
        });
    }
};
