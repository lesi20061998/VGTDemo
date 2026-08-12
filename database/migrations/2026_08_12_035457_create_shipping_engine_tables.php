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
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable(); // e.g. viettel_post
            $table->enum('type', ['local', 'api', 'hybrid'])->default('local');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_rate_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_carrier_id')->constrained('shipping_carriers')->cascadeOnDelete();
            $table->string('version_name'); // e.g. Bảng giá tháng 7/2026
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. Nội thành HCM
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_zone_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->string('province_code')->nullable();
            $table->string('district_code')->nullable();
            $table->string('ward_code')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_rate_version_id')->constrained('shipping_rate_versions')->cascadeOnDelete();
            $table->string('name'); // e.g. Freeship nội thành
            $table->integer('priority')->default(0); // higher number = higher priority
            $table->enum('action_type', ['override', 'add', 'subtract', 'free'])->default('override');
            $table->decimal('fee', 15, 2)->default(0);
            $table->boolean('is_surcharge')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_rule_id')->constrained('shipping_rules')->cascadeOnDelete();
            $table->enum('condition_type', ['order_value', 'distance', 'weight', 'zone', 'cod']);
            $table->enum('operator', ['>=', '<=', '=', '>', '<', 'between', 'in', '!=']);
            $table->decimal('value_1', 15, 2)->nullable();
            $table->decimal('value_2', 15, 2)->nullable();
            $table->foreignId('zone_id')->nullable()->constrained('shipping_zones')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rule_conditions');
        Schema::dropIfExists('shipping_rules');
        Schema::dropIfExists('shipping_zone_locations');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('shipping_rate_versions');
        Schema::dropIfExists('shipping_carriers');
    }
};
