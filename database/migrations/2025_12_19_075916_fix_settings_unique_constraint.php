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
        Schema::table('settings', function (Blueprint $table) {
            // Drop the existing unique constraint on 'key' column
            $table->dropUnique(['key']);

            // Add a composite unique constraint on 'key' and 'tenant_id'
            $table->unique(['key', 'tenant_id'], 'settings_key_tenant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('settings_key_tenant_unique');

            // Restore the original unique constraint on 'key' column
            $table->unique('key');
        });
    }
};
