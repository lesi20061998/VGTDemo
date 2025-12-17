<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products_enhanced', function (Blueprint $table) {
            if (! Schema::hasColumn('products_enhanced', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('id');
                $table->index('project_id');
            }
        });
    }

    public function down()
    {
        Schema::table('products_enhanced', function (Blueprint $table) {
            if (Schema::hasColumn('products_enhanced', 'project_id')) {
                $table->dropIndex(['project_id']);
                $table->dropColumn('project_id');
            }
        });
    }
};
