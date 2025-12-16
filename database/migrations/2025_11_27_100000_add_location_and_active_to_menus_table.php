<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'location')) {
                $table->string('location')->default('header')->after('slug');
            }
            if (!Schema::hasColumn('menus', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('location');
            }
            if (!Schema::hasColumn('menus', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            }
        });

        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('menu_id');
                $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('cascade');
            }
            if (!Schema::hasColumn('menu_items', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['location', 'is_active', 'tenant_id']);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'tenant_id']);
        });
    }
};