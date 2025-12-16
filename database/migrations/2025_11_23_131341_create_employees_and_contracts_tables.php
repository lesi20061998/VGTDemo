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
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Mã nhân sự: sivgt, abc123');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('position')->nullable()->comment('Vị trí: Dev, Designer');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('contracts')) {
            Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('website_id')->nullable();
            $table->string('contract_code')->unique()->comment('Mã hợp đồng: HD321');
            $table->string('full_code')->unique()->comment('sivgt.domain.com/HD321');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('employees');
    }
};
