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
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('client_name')->nullable()->change();
            $table->date('deadline')->nullable()->change();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('client_name')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('deadline')->nullable()->change();
            $table->string('subdomain')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('client_name')->nullable(false)->change();
            $table->date('deadline')->nullable(false)->change();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('client_name')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
            $table->date('deadline')->nullable(false)->change();
            $table->string('subdomain')->nullable(false)->change();
        });
    }
};
