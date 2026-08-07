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
        Schema::create('briefs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('client_name');
            $table->text('requirements');
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->foreignId('account_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('briefs');
    }
};
