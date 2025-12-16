<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_enhanced', function (Blueprint $table) {
            if (!Schema::hasColumn('products_enhanced', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('products_enhanced', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('products_enhanced', 'focus_keyword')) {
                $table->string('focus_keyword')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('products_enhanced', 'schema_type')) {
                $table->string('schema_type')->nullable()->after('focus_keyword');
            }
            if (!Schema::hasColumn('products_enhanced', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('schema_type');
            }
            if (!Schema::hasColumn('products_enhanced', 'noindex')) {
                $table->boolean('noindex')->default(false)->after('canonical_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products_enhanced', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'focus_keyword', 'schema_type', 'canonical_url', 'noindex']);
        });
    }
};
