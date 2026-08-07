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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('external_domain')->nullable()->after('subdomain')->comment('Domain of the exported site to sync data to');
            $table->boolean('sync_enabled')->default(false)->after('external_domain')->comment('Enable pushing updates to external_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['external_domain', 'sync_enabled']);
        });
    }
};
