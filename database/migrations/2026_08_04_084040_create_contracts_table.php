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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('client_name')->nullable();
            $table->string('service_type')->default('website'); // website, design, branding, photography, other
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Domain & Hosting fields (primarily for websites)
            $table->string('domain_name')->nullable();
            $table->date('domain_purchase_date')->nullable();
            $table->string('hosting_provider')->nullable();
            $table->date('hosting_start_date')->nullable();

            $table->decimal('contract_value', 15, 2)->nullable()->default(0);
            $table->json('attachments')->nullable(); // For storing multiple contract images
            $table->string('status')->default('pending'); // pending, active, completed, cancelled
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
