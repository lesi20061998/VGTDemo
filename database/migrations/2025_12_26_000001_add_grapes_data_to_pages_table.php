<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add GrapesJS columns to posts table (pages are stored in posts with post_type='page')
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'grapes_data')) {
                $table->longText('grapes_data')->nullable()->after('content');
            }
            if (!Schema::hasColumn('posts', 'custom_css')) {
                $table->text('custom_css')->nullable()->after('grapes_data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'grapes_data')) {
                $table->dropColumn('grapes_data');
            }
            if (Schema::hasColumn('posts', 'custom_css')) {
                $table->dropColumn('custom_css');
            }
        });
    }
};
