<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Add tenant_id if missing
            if (! Schema::hasColumn('settings', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            }

            // Add payload column if missing
            if (! Schema::hasColumn('settings', 'payload')) {
                $table->json('payload')->nullable()->after('key');
            }

            // Add group column if missing
            if (! Schema::hasColumn('settings', 'group')) {
                $table->string('group')->nullable()->after('payload');
            }

            // Add locked column if missing
            if (! Schema::hasColumn('settings', 'locked')) {
                $table->boolean('locked')->default(false)->after('group');
            }
        });

        // Migrate data from 'value' to 'payload' if value column exists
        if (Schema::hasColumn('settings', 'value') && Schema::hasColumn('settings', 'payload')) {
            \DB::table('settings')
                ->whereNull('payload')
                ->whereNotNull('value')
                ->update([
                    'payload' => \DB::raw("JSON_OBJECT('value', value)"),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
            if (Schema::hasColumn('settings', 'payload')) {
                $table->dropColumn('payload');
            }
            if (Schema::hasColumn('settings', 'group')) {
                $table->dropColumn('group');
            }
            if (Schema::hasColumn('settings', 'locked')) {
                $table->dropColumn('locked');
            }
        });
    }
};
