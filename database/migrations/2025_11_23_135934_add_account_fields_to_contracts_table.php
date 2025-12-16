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
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_active');
            $table->string('client_name')->nullable()->after('contract_code');
            $table->string('service_type')->nullable()->after('client_name');
            $table->text('requirements')->nullable()->after('service_type');
            $table->text('design_description')->nullable()->after('requirements');
            $table->string('attachments')->nullable()->after('design_description');
            $table->date('deadline')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['status', 'client_name', 'service_type', 'requirements', 'design_description', 'attachments', 'deadline']);
        });
    }
};
