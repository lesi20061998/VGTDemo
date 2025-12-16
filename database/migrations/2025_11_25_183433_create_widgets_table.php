<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('area_key');
            $table->string('widget_class');
            $table->string('title')->nullable();
            $table->json('config')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
