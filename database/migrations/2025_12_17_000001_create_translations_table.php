<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type'); // App\Models\Product, App\Models\Post, etc.
            $table->unsignedBigInteger('translatable_id');
            $table->string('locale', 5); // vi, en, zh, etc.
            $table->string('field'); // name, description, content, etc.
            $table->longText('value');
            $table->timestamps();

            $table->index(['translatable_type', 'translatable_id']);
            $table->index(['locale']);
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'translations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
