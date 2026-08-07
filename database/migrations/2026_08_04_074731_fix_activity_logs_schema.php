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
        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade')->after('user_id');
            }
            if (! Schema::hasColumn('activity_logs', 'model')) {
                $table->string('model')->nullable()->after('action');
            }
            if (! Schema::hasColumn('activity_logs', 'description')) {
                $table->text('description')->nullable()->after('model_id');
            }
            if (! Schema::hasColumn('activity_logs', 'properties')) {
                $table->json('properties')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['project_id', 'model', 'description', 'properties']);
        });
    }
};
