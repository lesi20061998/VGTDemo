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
        // Skip - website_id foreign key không tồn tại
        if (Schema::hasColumn('contracts', 'website_id')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('website_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('website_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
